<?php

namespace ChronopostHomeDelivery\Event;

class CartDeliveryModeEvent extends AbstractCartPostageEvent
{
    private ?string  $deliveryModeCode = null;

    public function getDeliveryModeCode(): ?string
    {
        return $this->deliveryModeCode;
    }

    public function setDeliveryModeCode(?string $deliveryModeCode): CartDeliveryModeEvent
    {
        $this->deliveryModeCode = $deliveryModeCode;

        return $this;
    }
}
