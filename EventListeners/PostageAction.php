<?php

namespace ChronopostHomeDelivery\EventListeners;

use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Event\CartAmountEvent;
use ChronopostHomeDelivery\Event\CartDeliveryModeEvent;
use ChronopostHomeDelivery\Event\CartPostageWeightEvent;
use ChronopostHomeDelivery\Event\ChronopostHomeDeliveryEvents;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PostageAction implements EventSubscriberInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ChronopostHomeDeliveryEvents::CART_POSTAGE_WEIGHT => ['getWeight', 128],
            ChronopostHomeDeliveryEvents::CART_DELIVERY_MODE  => [['getDeliveryMode', 128]],
            ChronopostHomeDeliveryEvents::CART_AMOUNT         => [['getCartAmount', 128]]
        ];
    }

    /**
     * This method provides the Cart weight
     * @param CartPostageWeightEvent $event
     * @return void
     * @throws PropelException
     */
    public function getWeight(CartPostageWeightEvent $event): void
    {
        $cart = $event->getCart();
        $event->setWeight($cart->getWeight());
    }

    /**
     * Retrieves and sets the delivery mode code for the cart delivery event.
     *
     * This method extracts the delivery type from the current request. If no delivery type
     * is found in the request, it attempts to retrieve it from the session. Once identified,
     * the delivery type is set as the delivery mode code for the provided event.
     *
     * @param CartDeliveryModeEvent $event The event containing the cart delivery data.
     */
    public function getDeliveryMode(CartDeliveryModeEvent $event): void
    {
        /** Get the delivery type of an ongoing order by looking at the request */
        $deliveryType = ChronopostHomeDelivery::getDeliveryType(
            $this->requestStack->getCurrentRequest()
        );

        /** If no delivery type was found, search again in the session. */
        if (null === $deliveryType) {
            $deliveryType = ChronopostHomeDelivery::getDeliveryType(
                $this->requestStack->getCurrentRequest()->getSession()
            );
        }

        if (!empty($deliveryType)) {
            $event->setDeliveryModeCode($deliveryType);
        }
    }

    /**
     * This method provides the Cart amount
     * @param CartAmountEvent $event
     * @return void
     * @throws PropelException
     */
    public function getCartAmount(CartAmountEvent $event): void
    {
        $cartAmount = $event->getCart()
            ->getTaxedAmount($event->getCountry());
        $event->setTaxedAmount($cartAmount);
    }
}
