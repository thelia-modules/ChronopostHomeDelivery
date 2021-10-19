<?php

namespace ChronopostHomeDelivery\Smarty\Plugins;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
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
    protected $requestStack;
    protected $dispatcher;

    /**
     * ChronopostHomeDeliveryDeliveryType constructor.
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

        $request = $this->requestStack->getCurrentRequest();
        $cartWeight = $request->getSession()->getSessionCart($this->dispatcher)->getWeight();
        $cartAmount = $request->getSession()->getSessionCart($this->dispatcher)->getTaxedAmount($country);

        try {

            $price = (new ChronopostHomeDelivery)->getMinPostage(
                $country,
                $cartWeight,
                $cartAmount,
                $deliveryMode,
                $request->getSession()->getLang()->getLocale()
            );

            $consumedCouponsCodes = $request->getSession()->getConsumedCoupons();

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
