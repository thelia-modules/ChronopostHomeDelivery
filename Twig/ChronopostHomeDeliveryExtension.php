<?php

namespace ChronopostHomeDelivery\Twig;

use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Log\Tlog;
use Thelia\Model\CountryQuery;
use Thelia\Model\CouponQuery;
use Thelia\Module\Exception\DeliveryException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ChronopostHomeDeliveryExtension extends AbstractExtension
{
    private $requestStack;
    private $dispatcher;

    /**
     * Constructeur de l'extension Twig.
     *
     * @param RequestStack $requestStack
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(RequestStack $requestStack, EventDispatcherInterface $dispatcher = null)
    {
        $this->requestStack = $requestStack;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Définir les fonctions Twig disponibles.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('chronopostHomeDeliveryDeliveryType', [$this, 'getDeliveryType']),
            new TwigFunction('chronopostHomeDeliveryDeliveryPrice', [$this, 'getDeliveryPrice']),
            new TwigFunction('chronopostHomeDeliveryGetDeliveryTypesStatusKeys', [$this, 'getDeliveryTypesStatusKeys']),
        ];
    }

    /**
     * Obtenir les types de livraison et leurs statuts.
     *
     * @return array
     */
    public function getDeliveryType()
    {
        $deliveryTypes = [];
        foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $deliveryTypeName => $statusKey) {
            $deliveryTypes['is' . $deliveryTypeName . 'Enabled'] = (bool)ChronopostHomeDelivery::getConfigValue($statusKey);
        }
        return $deliveryTypes;
    }

    /**
     * Calculer le prix de la livraison.
     *
     * @param string $deliveryMode
     * @param int $countryId
     * @return float|null
     */
    public function getDeliveryPrice(string $deliveryMode, int $countryId): ?float
    {
        $country = CountryQuery::create()->findOneById($countryId);
        $request = $this->requestStack->getCurrentRequest();
        $cart = $request->getSession()->getSessionCart($this->dispatcher);
        $cartWeight = $cart->getWeight();
        $cartAmount = $cart->getTaxedAmount($country);

        try {
            $price = (new ChronopostHomeDelivery())->getMinPostage(
                $country,
                $cartWeight,
                $cartAmount,
                $deliveryMode,
                $request->getSession()->getLang()->getLocale()
            );

            $consumedCouponsCodes = $request->getSession()->getConsumedCoupons();

            foreach ($consumedCouponsCodes as $consumedCouponCode) {
                $coupon = CouponQuery::create()
                    ->filterByCode($consumedCouponCode)
                    ->findOne();

                if ($coupon !== null && $coupon->getIsRemovingPostage()) {
                    $price = 0;
                }
            }

            return $price;
        } catch (DeliveryException $ex) {
            return Tlog::getInstance()->error($ex->getMessage());
        }
    }

    /**
     * Obtenir les clés de statut des types de livraison.
     *
     * @return array
     */
    public function getDeliveryTypesStatusKeys(): array
    {
        return ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys();
    }
}
