<?php

namespace ChronopostHomeDelivery\Loop;


use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryModeQuery;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Base\LangQuery;

/**
 *
 * @method int|null getLangId()
 * @method int|null getEditI18n()
 * @method string[] getByCode()
 */
class ChronopostHomeDeliveryDeliveryMode extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Unused
     */
    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection(
            Argument::createAnyTypeArgument('lang_id'),
            Argument::createBooleanTypeArgument('edit_i18n'),
            Argument::createAlphaNumStringListTypeArgument('by_code')
        );
    }

    /**
     * @return ChronopostHomeDeliveryDeliveryModeQuery|ModelCriteria
     */
    public function buildModelCriteria(): ChronopostHomeDeliveryDeliveryModeQuery|ModelCriteria
    {
        $q = ChronopostHomeDeliveryDeliveryModeQuery::create()
            ->filterEnabledQuery($this->getByCode() ?? []);

        return $q;
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        $session = $this->getCurrentRequest()->getSession();

        $lang = $session->get('thelia.current.lang');
        if ($this->getBackendContext()) {
            $lang = $session->get('thelia.current.admin_lang');
        }
        if (null !== $langId = $this->getLangId()){
            $lang = LangQuery::create()->findPk($langId);
        }
        if ($this->getEditI18n()){
            $lang = $session->get('thelia.admin.edition.lang');
        }

        /** @var \ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryMode $mode */
        foreach ($loopResult->getResultDataCollection() as $mode) {
            $loopResultRow = new LoopResultRow($mode);
            $loopResultRow
                ->set("ID", $mode->getId())
                ->set("TITLE", $mode->setLocale($lang->getLocale())->getTitle())
                ->set("CODE", $mode->getCode())
                ->set("FREESHIPPING_ACTIVE", $mode->getFreeshippingActive())
                ->set("FREESHIPPING_FROM", $mode->getFreeshippingFrom());
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}
