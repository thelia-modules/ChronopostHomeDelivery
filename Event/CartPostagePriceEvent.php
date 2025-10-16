<?php

namespace ChronopostHomeDelivery\Event;

use Thelia\Model\OrderPostage;

class CartPostagePriceEvent extends AbstractCartPostageEvent
{
    private OrderPostage $orderPostage;

    public function getOrderPostage(): OrderPostage
    {
        return $this->orderPostage;
    }

    public function setOrderPostage(OrderPostage $orderPostage): CartPostagePriceEvent
    {
        $this->orderPostage = $orderPostage;

        return $this;
    }
}
