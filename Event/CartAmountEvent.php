<?php

namespace ChronopostHomeDelivery\Event;

use Thelia\Model\Country;

class CartAmountEvent extends AbstractCartPostageEvent
{
    private float $taxedAmount = 0.0;
    private Country $country;

    public function getTaxedAmount(): float
    {
        return $this->taxedAmount;
    }

    public function setTaxedAmount(float $taxedAmount): CartAmountEvent
    {
        $this->taxedAmount = $taxedAmount;

        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): CartAmountEvent
    {
        $this->country = $country;

        return $this;
    }
}
