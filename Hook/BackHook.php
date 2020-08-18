<?php

namespace ChronopostHomeDelivery\Hook;


use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class BackHook extends BaseHook
{
    public function onModuleConfiguration(HookRenderEvent $event)
    {
        $event->add($this->render('ChronopostHomeDelivery/ChronopostHomeDeliveryConfig.html'));
    }

    public function onModuleConfigJs(HookRenderEvent $event)
    {
        $event->add($this->render('ChronopostHomeDelivery/module-config-js.html'));
    }
}