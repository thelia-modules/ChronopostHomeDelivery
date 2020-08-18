<?php

namespace ChronopostHomeDelivery\Loop;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryModeQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class ChronopostHomeDeliveryDeliveryMode extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Unused
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection();
    }

    /**
     * @return ChronopostHomeDeliveryDeliveryModeQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $config = ChronopostHomeDeliveryConst::getConfig();
        $modes = ChronopostHomeDeliveryDeliveryModeQuery::create();

        $enabledDeliveryTypes = [];
        foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $deliveryTypeName => $statusKey) {
            $enabledDeliveryTypes[] = $config[$statusKey] ? ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES[$deliveryTypeName] : '';
        }

        $modes->filterByCode($enabledDeliveryTypes, Criteria::IN);

        return $modes;
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryMode $mode */
        foreach ($loopResult->getResultDataCollection() as $mode) {
            $loopResultRow = new LoopResultRow($mode);
            $loopResultRow
                ->set("ID", $mode->getId())
                ->set("TITLE", $mode->getTitle())
                ->set("CODE", $mode->getCode())
                ->set("FREESHIPPING_ACTIVE", $mode->getFreeshippingActive())
                ->set("FREESHIPPING_FROM", $mode->getFreeshippingFrom());
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}