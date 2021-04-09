<?php

namespace ChronopostHomeDelivery\EventListeners;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryOrder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;



class SetDeliveryType implements EventSubscriberInterface
{
    /** @var Request */
    protected $request;

    /**
     * SetDeliveryType constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $id
     * @return bool
     */
    protected function checkModule($id)
    {
        return $id == ChronopostHomeDelivery::getModuleId();
    }

    /**
     * @param OrderEvent $orderEvent
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function saveChronopostHomeDeliveryOrder(OrderEvent $orderEvent)
    {
        if ($this->checkModule($orderEvent->getOrder()->getDeliveryModuleId())) {

            $request = $this->getRequest();
            $chronopostOrder = new ChronopostHomeDeliveryOrder();

            $orderId = $orderEvent->getOrder()->getId();

            foreach (ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES as $name => $code) {
                if ($code === $request->getSession()->get('ChronopostHomeDeliveryDeliveryType')) {
                    $chronopostOrder
                        ->setDeliveryType($name)
                        ->setDeliveryCode($code)
                    ;
                }
            }

            $chronopostOrder
                ->setOrderId($orderId)
                ->save();
        }

        return ;
    }

    /**
     * @param OrderEvent $orderEvent
     * @return null
     */
    public function setChronopostHomeDeliveryDeliveryType(OrderEvent $orderEvent)
    {
        if ($this->checkModule($orderEvent->getDeliveryModule())) {
            $request = $this->getRequest();

            $request->getSession()->set('ChronopostAddressId', $orderEvent->getDeliveryAddress());
            $request->getSession()->set('ChronopostHomeDeliveryDeliveryType', $request->get('deliveryModuleOptionCode'));
        }

        return ;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_SET_DELIVERY_MODULE => array('setChronopostHomeDeliveryDeliveryType', 64),
            TheliaEvents::ORDER_BEFORE_PAYMENT => array('saveChronopostHomeDeliveryOrder', 256),
        );
    }
}