<?php

namespace ChronopostHomeDelivery\Smarty\Plugins;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\CountryArea;
use Thelia\Model\CountryQuery;
use Thelia\Model\Coupon;
use Thelia\Model\CouponQuery;
use Thelia\Module\Exception\DeliveryException;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class ChronopostHomeDeliveryDeliveryType extends AbstractSmartyPlugin
{
    protected $request;
    protected $dispatcher;

    /**
     * ChronopostHomeDeliveryDeliveryType constructor.
     *
     * @param Request $request
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(Request $request, EventDispatcherInterface $dispatcher = null)
    {
        $this->request = $request;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return array|SmartyPluginDescriptor[]
     */
    public function getPluginDescriptors()
    {
        return array(
            new SmartyPluginDescriptor("function", "chronopostHomeDeliveryDeliveryType", $this, "chronopostHomeDeliveryDeliveryType"),
            new SmartyPluginDescriptor("function", "chronopostHomeDeliveryDeliveryPrice", $this, "chronopostHomeDeliveryDeliveryPrice"),
            new SmartyPluginDescriptor("function", "chronopostHomeDeliveryGetDeliveryTypesStatusKeys", $this, "chronopostHomeDeliveryGetDeliveryTypesStatusKeys"),
        );
    }

    /**
     * @param $params
     * @param $smarty
     * @throws PropelException
     */
    public function chronopostHomeDeliveryDeliveryPrice($params, $smarty)
    {
        $deliveryMode = $params["delivery-mode"];
        $country = CountryQuery::create()->findOneById($params["country"]);

        $cartWeight = $this->request->getSession()->getSessionCart($this->dispatcher)->getWeight();
        $cartAmount = $this->request->getSession()->getSessionCart($this->dispatcher)->getTaxedAmount($country);

        try {

            $countryAreas = $country->getCountryAreas();
            $areasArray = [];

            /** @var CountryArea $countryArea */
            foreach ($countryAreas as $countryArea) {
                $areasArray[] = $countryArea->getAreaId();
            }

            $price = (new ChronopostHomeDelivery)->getMinPostage(
                $areasArray,
                $cartWeight,
                $cartAmount,
                $deliveryMode
            );

            $consumedCouponsCodes = $this->request->getSession()->getConsumedCoupons();

            foreach ($consumedCouponsCodes as $consumedCouponCode)  {
                $coupon = CouponQuery::create()
                    ->filterByCode($consumedCouponCode)
                    ->findOne();

                /** @var Coupon $coupon */
                if(null  !== $coupon){
                    if($coupon->getIsRemovingPostage()){
                        $price = 0;
                    }
                }
            }

        } catch (DeliveryException $ex) {
            $smarty->assign('isValidMode', false);
        }

        $smarty->assign('chronopostHomeDeliveryDeliveryModePrice', $price);

    }

    /**
     * @param $params
     * @param $smarty
     */
    public function chronopostHomeDeliveryDeliveryType($params, $smarty)
    {
        foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $deliveryTypeName => $statusKey) {
            $smarty->assign('is' . $deliveryTypeName . 'Enabled', (bool)ChronopostHomeDelivery::getConfigValue($statusKey));
        }
    }

    /**
     * @param $params
     * @param $smarty
     */
    public function chronopostHomeDeliveryGetDeliveryTypesStatusKeys($params, $smarty)
    {
        $smarty->assign('chronopostHomeDeliveryDeliveryTypesStatusKeys', ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys());
    }

}
