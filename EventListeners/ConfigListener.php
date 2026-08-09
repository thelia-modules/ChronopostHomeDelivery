<?php

namespace ChronopostHomeDelivery\EventListeners;

use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryModeQuery;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryPriceQuery;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\ModuleConfigQuery;
use function Symfony\Component\Translation\t;

class ConfigListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'module.config' => 'onModuleConfigure'
        ];
    }

    public function onModuleConfigure(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if ($subject !== "HealthStatus") {
            throw new \RuntimeException('Event subject does not match expected value');
        }

        $shippingZoneConfig = AreaDeliveryModuleQuery::create()
            ->filterByDeliveryModuleId(ChronopostHomeDelivery::getModuleId())
            ->find();

        $configModule = ModuleConfigQuery::create()
            ->filterByName(['chronopost_home_delivery_password', 'chronopost_home_delivery_code'])
            ->find();

        $freeShipping = ChronopostHomeDeliveryDeliveryModeQuery::create()
            ->filterByCode(['1', '16', '17', '44', '2R', '2', '2S'])
            ->find();

        $slicesConfig = ChronopostHomeDeliveryPriceQuery::create()
            ->find();

        $moduleConfig = [];
        $moduleConfig['module'] = ChronopostHomeDelivery::getModuleCode();
        $configsCompleted = true;

        if ($configModule->count() === 0) {
            $configsCompleted = false;
        }

        if ($shippingZoneConfig->count() === 0) {
            $configsCompleted = false;
        }

        foreach ($configModule as $config) {
            if ($config->getValue() === null || $config->getValue() === "") {
                $configsCompleted = false;
                break;
            }
        }

        $hasFreeShipping = false;
        foreach ($freeShipping as $shipping) {
            if ($shipping->getFreeshippingActive() === true) {
                $hasFreeShipping = true;
                break;
            }
        }

        $hasFreeShippingFrom = false;
        foreach ($freeShipping as $shipping) {
            if ($shipping->getFreeshippingFrom() !== null) {
                $hasFreeShippingFrom = true;
                break;
            }
        }

        $hasSlices = false;
        if ($slicesConfig->count() > 0) {
            $hasSlices = true;
        }

        if (!$hasFreeShipping && !$hasSlices && !$hasFreeShippingFrom) {
            $configsCompleted = false;
        }

        $moduleConfig['completed'] = $configsCompleted;

        $event->setArgument('chronopost.home.delivery.config', $moduleConfig);
    }


}