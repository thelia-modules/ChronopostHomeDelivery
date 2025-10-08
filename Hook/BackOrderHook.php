<?php

namespace ChronopostHomeDelivery\Hook;

use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryOrderQuery;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ModuleQuery;

class BackOrderHook extends BaseHook
{
    public function onOrderEditDeliveryModuleBottom(HookRenderEvent $event): void
    {
        $orderId = $event->getArgument("order_id");
        $moduleId = $event->getArgument("module_id");
        $module = ModuleQuery::create()->findPk($moduleId);
        if ($module->getCode() !== "ChronopostHomeDelivery") return;
        $chronopostOrder = ChronopostHomeDeliveryOrderQuery::create()->filterByOrderId($orderId)->findOne();
        if (!$chronopostOrder) return;
        $event->add(
            $this->render(
                'ChronopostHomeDelivery/order-edit.delivery-module-bottom.html',
                [
                    'delivery_type' => $chronopostOrder->getDeliveryType(),
                ]
            ));
    }

    public static function getSubscribedHooks(): array
    {
        return [
            "order-edit.delivery-module-bottom" => [
                [
                    "type" => "back",
                    "method" => "onOrderEditDeliveryModuleBottom"
                ],
            ],
        ];
    }
}
