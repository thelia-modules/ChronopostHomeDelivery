<?php


namespace ChronopostHomeDelivery\EventListeners;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use OpenApi\Events\DeliveryModuleOptionEvent;
use OpenApi\Events\OpenApiEvents;
use OpenApi\Model\Api\DeliveryModuleOption;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;
use Thelia\Model\CountryArea;
use Thelia\Model\PickupLocation;
use Thelia\Module\Exception\DeliveryException;

class APIListener implements EventSubscriberInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get the list of delivery types
     *
     * @param DeliveryModuleOptionEvent $deliveryModuleOptionEvent
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getDeliveryModuleOptions(DeliveryModuleOptionEvent $deliveryModuleOptionEvent)
    {
        if ($deliveryModuleOptionEvent->getModule()->getId() !== ChronopostHomeDelivery::getModuleId()) {
            return ;
        }

        $activatedDeliveryTypes = ChronopostHomeDelivery::getActivatedDeliveryTypes();

        foreach (ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES as $name => $code) {
            if (!in_array($code, $activatedDeliveryTypes, false)) {
                continue ;
            }

            $isValid = true;
            $postage = null;
            $postageTax = null;

            try {
                $module = new ChronopostHomeDelivery();
                $country = $deliveryModuleOptionEvent->getCountry();

                if (empty($module->getAllAreasForCountry($country))) {
                    throw new DeliveryException(Translator::getInstance()->trans("Your delivery country is not covered by Chronopost"));
                }

                $countryAreas = $country->getCountryAreas();
                $areasArray = [];

                /** @var CountryArea $countryArea */
                foreach ($countryAreas as $countryArea) {
                    $areasArray[] = $countryArea->getAreaId();
                }

                $postage = $module->getMinPostage(
                    $areasArray,
                    $deliveryModuleOptionEvent->getCart()->getWeight(),
                    $deliveryModuleOptionEvent->getCart()->getTaxedAmount($country),
                    $code
                );

                $postageTax = 0; //TODO

                if (null === $postage) {
                    $isValid = false;
                }

            } catch (\Exception $exception) {
                $isValid = false;
            }

            $minimumDeliveryDate = ''; // TODO (with a const array code => timeToDeliver to calculate delivery date from day of order)
            $maximumDeliveryDate = ''; // TODO (with a const array code => timeToDeliver to calculate delivery date from day of order)

            /** @var DeliveryModuleOption $deliveryModuleOption */
            $deliveryModuleOption = ($this->container->get('open_api.model.factory'))->buildModel('DeliveryModuleOption');
            $deliveryModuleOption
                ->setCode($code)
                ->setValid($isValid)
                ->setTitle($name)
                ->setImage('')
                ->setMinimumDeliveryDate($minimumDeliveryDate)
                ->setMaximumDeliveryDate($maximumDeliveryDate)
                ->setPostage($postage)
                ->setPostageTax($postageTax)
                ->setPostageUntaxed($postage - $postageTax)
            ;

            $deliveryModuleOptionEvent->appendDeliveryModuleOptions($deliveryModuleOption);
        }
    }

    public static function getSubscribedEvents()
    {
        $listenedEvents = [];

        /** Check for old versions of Thelia where the events used by the API didn't exists */
        if (class_exists(DeliveryModuleOptionEvent::class)) {
            $listenedEvents[OpenApiEvents::MODULE_DELIVERY_GET_OPTIONS] = array("getDeliveryModuleOptions", 131);
        }

        return $listenedEvents;
    }
}