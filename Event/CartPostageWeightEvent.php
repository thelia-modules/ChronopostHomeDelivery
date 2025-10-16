<?php

namespace ChronopostHomeDelivery\Event;



use Thelia\Core\Event\Cart\CartEvent;

class CartPostageWeightEvent extends CartEvent
{
    private float $weight = 0.0;

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): CartPostageWeightEvent
    {
        $this->weight = $weight;

        return $this;
    }
}
