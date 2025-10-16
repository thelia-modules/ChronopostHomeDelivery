<?php

namespace ChronopostHomeDelivery\Event;

use Thelia\Core\Event\DefaultActionEvent;
use Thelia\Model\Cart;

abstract class AbstractCartPostageEvent extends DefaultActionEvent
{
    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): AbstractCartPostageEvent
    {
        $this->cart = $cart;

        return $this;
    }
}
