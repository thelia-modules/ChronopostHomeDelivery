<?php

namespace ChronopostHomeDelivery\Model;

use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use ChronopostHomeDelivery\Model\Base\ChronopostHomeDeliveryDeliveryModeQuery as BaseChronopostHomeDeliveryDeliveryModeQuery;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Skeleton subclass for performing query and update operations on the 'chronopost_home_delivery_delivery_mode' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ChronopostHomeDeliveryDeliveryModeQuery extends BaseChronopostHomeDeliveryDeliveryModeQuery
{
    /**
     * Returns delivery modes only activated
     * @param array $filterByCode example ['2R','44','2S']
     * @return ChronopostHomeDeliveryDeliveryModeQuery
     */
    public function filterEnabledQuery(array $filterByCode = []): ChronopostHomeDeliveryDeliveryModeQuery
    {
        $config               = ChronopostHomeDeliveryConst::getConfig();
        $modes                = self::create();
        $byCodeIsOverridden   = !empty($filterByCode);
        $enabledDeliveryTypes = $byCodeIsOverridden ? $filterByCode : [];

        if (!$byCodeIsOverridden) {
            foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $deliveryTypeName => $statusKey) {
                $enabledDeliveryTypes[] = $config[$statusKey]
                    ? ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES[$deliveryTypeName]
                    : '';
            }
        }

        if (!empty($enabledDeliveryTypes)) {
            $modes->filterByCode($enabledDeliveryTypes, Criteria::IN);
        }

        return $modes;
    }
} // ChronopostHomeDeliveryDeliveryModeQuery
