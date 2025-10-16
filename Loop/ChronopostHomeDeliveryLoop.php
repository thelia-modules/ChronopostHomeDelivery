<?php

namespace ChronopostHomeDelivery\Loop;


use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryPrice;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryPriceQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class ChronopostHomeDeliveryLoop
 * @package ChronopostHomeDelivery\Loop
 *
 * @method integer getAreaId
 * @method integer getDeliveryModeId
 */
class ChronopostHomeDeliveryLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('area_id', null, false),
            Argument::createIntTypeArgument('delivery_mode_id', null, false)
        );
    }

    /**
     * @return ChronopostHomeDeliveryPriceQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $areaId = $this->getAreaId();
        $modeId = $this->getDeliveryModeId();

        $areaPrices = ChronopostHomeDeliveryPriceQuery::create();
        if (!is_null($areaId)) {
            $areaPrices->filterByAreaId($areaId);
        }
        if (!is_null($modeId)) {
            $areaPrices->filterByDeliveryModeId($modeId);
        }

        return $areaPrices->orderByWeightMax();
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var ChronopostHomeDeliveryPrice $price */
        foreach ($loopResult->getResultDataCollection() as $price) {
            $loopResultRow = new LoopResultRow($price);

            $loopResultRow
                ->set("SLICE_ID", $price->getId())
                ->set("MAX_WEIGHT", $price->getWeightMax())
                ->set("MAX_PRICE", $price->getPriceMax())
                ->set("PRICE", $price->getPrice())
                ->set("FRANCO", $price->getFrancoMinPrice())
                ->set("AREA_ID", $price->getAreaId())
            ;
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}
