<?php

namespace ChronopostHomeDelivery\EventListeners;

use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\TheliaEvents;
use Symfony\Component\HttpFoundation\Request;

class Postage implements EventSubscriberInterface
{
    public function __construct(private RequestStack $requestStack)
    {}

    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }
    public function moduleDeliveryPostage(DeliveryPostageEvent $event)
    {
        if (!$this->checkModule($event->getModule())) {
            return;
        }
        $request = $this->getRequest();
        $deliveryType = $request->getSession()->get('ChronopostHomeDeliveryDeliveryType');
        if (!in_array($deliveryType, ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES)) {
            return;
        }
        $postage = (new ChronopostHomeDelivery())->getMinPostage($event->getCountry(), $event->getCart()->getWeight(), $event->getCart()->getTaxedAmount($event->getCountry()), $deliveryType, $request->getLocale());
        $event->setPostage($postage);

    }

    protected function checkModule($module)
    {
        return $module instanceof ChronopostHomeDelivery;
    }

    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::MODULE_DELIVERY_GET_POSTAGE => ['moduleDeliveryPostage', 128],
        ];
    }
}
