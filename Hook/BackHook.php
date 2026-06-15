<?php

namespace ChronopostHomeDelivery\Hook;

use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use ChronopostHomeDelivery\Form\ChronopostHomeDeliveryConfigurationForm;
use ChronopostHomeDelivery\Form\ChronopostHomeDeliveryFreeShippingForm;
use ChronopostHomeDelivery\Form\ChronopostHomeDeliveryModeForm;
use ChronopostHomeDelivery\Form\ChronopostHomeDeliveryTaxRuleForm;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryAreaFreeshippingQuery;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryModeQuery;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryPriceQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Model\AreaQuery;
use Thelia\Model\CurrencyQuery;
use Thelia\Model\LangQuery;

class BackHook extends BaseHook
{
    public function __construct(
        private readonly TheliaFormFactory $formFactory,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                ['type' => 'back', 'method' => 'onModuleConfiguration'],
            ],
            'module.config-js' => [
                ['type' => 'back', 'method' => 'onModuleConfigJs'],
            ],
        ];
    }

    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $locale = $this->getEditionLocale();
        $moduleId = ChronopostHomeDelivery::getModuleId();

        $configForm = $this->formFactory
            ->createForm(ChronopostHomeDeliveryConfigurationForm::getName())
            ->createView()
            ->getView();

        $taxRuleForm = $this->formFactory
            ->createForm(ChronopostHomeDeliveryTaxRuleForm::getName())
            ->createView()
            ->getView();

        $event->add($this->render($this->resolveTemplateName('ChronopostHomeDelivery/ChronopostHomeDeliveryConfig'), [
            'configForm' => $configForm,
            'taxRuleForm' => $taxRuleForm,
            'deliveryModes' => $this->getDeliveryModes($locale),
            'areas' => $this->getAreas($moduleId),
            'currencySymbol' => $this->getDefaultCurrencySymbol(),
            'moduleId' => $moduleId,
        ]));
    }

    public function onModuleConfigJs(HookRenderEvent $event): void
    {
        $event->add($this->render($this->resolveTemplateName('ChronopostHomeDelivery/module-config-js')));
    }

    /**
     * Append the current parser extension so the same hook serves the Smarty (default)
     * and Twig (default-twig) back-office templates: Smarty -> ".html", Twig -> ".html.twig".
     */
    private function resolveTemplateName(string $baseName): string
    {
        $extension = ParserResolver::getCurrentParser()?->getFileExtension() ?? 'html';

        return $baseName.'.'.$extension;
    }

    /**
     * Reproduces {loop type="chronopost.home.delivery.delivery.mode" edit_i18n=1}:
     * enabled delivery modes with i18n title + freeshipping data + per-mode forms.
     */
    private function getDeliveryModes(string $locale): array
    {
        $config = ChronopostHomeDeliveryConst::getConfig();

        $enabledCodes = [];
        foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $deliveryTypeName => $statusKey) {
            $enabledCodes[] = $config[$statusKey]
                ? ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES[$deliveryTypeName]
                : '';
        }

        $modes = ChronopostHomeDeliveryDeliveryModeQuery::create()
            ->filterByCode($enabledCodes, Criteria::IN)
            ->find();

        $result = [];
        foreach ($modes as $mode) {
            $modeForm = $this->formFactory
                ->createForm(
                    ChronopostHomeDeliveryModeForm::getName(),
                    data: ['delivery_mode_id' => $mode->getId(), 'delivery_mode_title' => $mode->setLocale($locale)->getTitle()]
                )
                ->createView()
                ->getView();

            $freeshippingForm = $this->formFactory
                ->createForm(
                    ChronopostHomeDeliveryFreeShippingForm::getName(),
                    data: ['delivery_mode' => $mode->getId(), 'freeshipping' => (bool) $mode->getFreeshippingActive()]
                )
                ->createView()
                ->getView();

            $result[] = [
                'id' => $mode->getId(),
                'title' => $mode->getTitle(),
                'code' => $mode->getCode(),
                'freeshipping_active' => (bool) $mode->getFreeshippingActive(),
                'freeshipping_from' => $mode->getFreeshippingFrom(),
                'modeForm' => $modeForm,
                'freeshippingForm' => $freeshippingForm,
            ];
        }

        return $result;
    }

    /**
     * Reproduces {loop type="area" module_id=... backend_context=true} restricted to the module's
     * delivery zones, plus the price slices and area-freeshipping cart amount per delivery mode.
     */
    private function getAreas(int $moduleId): array
    {
        $areas = AreaQuery::create()
            ->useAreaDeliveryModuleQuery()
                ->filterByDeliveryModuleId([$moduleId], Criteria::IN)
            ->endUse()
            ->find();

        $deliveryModes = ChronopostHomeDeliveryDeliveryModeQuery::create()->find();

        $result = [];
        foreach ($areas as $area) {
            $perMode = [];
            foreach ($deliveryModes as $mode) {
                $freeshipping = ChronopostHomeDeliveryAreaFreeshippingQuery::create()
                    ->filterByAreaId($area->getId())
                    ->filterByDeliveryModeId($mode->getId())
                    ->findOne();

                $slices = [];
                $sliceRows = ChronopostHomeDeliveryPriceQuery::create()
                    ->filterByDeliveryModeId($mode->getId())
                    ->filterByAreaId($area->getId())
                    ->orderByWeightMax()
                    ->find();

                foreach ($sliceRows as $slice) {
                    $slices[] = [
                        'slice_id' => $slice->getId(),
                        'max_weight' => $slice->getWeightMax(),
                        'max_price' => $slice->getPriceMax(),
                        'price' => $slice->getPrice(),
                    ];
                }

                $perMode[$mode->getId()] = [
                    'cart_amount' => $freeshipping?->getCartAmount(),
                    'slices' => $slices,
                ];
            }

            $result[] = [
                'id' => $area->getId(),
                'name' => $area->getName(),
                'modes' => $perMode,
            ];
        }

        return $result;
    }

    private function getDefaultCurrencySymbol(): string
    {
        $currency = CurrencyQuery::create()->findOneByByDefault(true);

        return $currency?->getSymbol() ?? '';
    }

    private function getEditionLocale(): string
    {
        $lang = $this->getRequest()?->getSession()?->get('thelia.admin.edition.lang');

        if (null === $lang) {
            $lang = LangQuery::create()->filterByByDefault(1)->findOne();
        }

        return $lang?->getLocale() ?? 'en_US';
    }
}
