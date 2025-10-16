<?php

namespace ChronopostHomeDelivery\Event;

class ChronopostHomeDeliveryEvents
{
    // Event to calculate Cart weight
    public const CART_POSTAGE_WEIGHT = 'chronopost_home_delivery.cart_postage_weight';
    // Event to determine delivery mode
    public const CART_DELIVERY_MODE = 'chronopost_home_delivery.cart_delivery_mode';
    // Event to calculate cart amount
    public const CART_AMOUNT = 'chronopost_home_delivery.cart_amount';
    // Event at end of shipping cost calculation
    public const CART_POSTAGE_PRICE = 'chronopost_home_delivery.cart_postage_price';
}
