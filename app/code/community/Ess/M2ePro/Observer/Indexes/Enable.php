<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Observer_Indexes_Enable extends Ess_M2ePro_Observer_Abstract
{
    //########################################

    public function process()
    {
        /** @var $index Ess_M2ePro_Model_Magento_Product_Index */
        $index = Mage::getSingleton('M2ePro/Magento_Product_Index');

        if (!$index->isIndexManagementEnabled()) {
            return;
        }

        $enabledIndexes = array();

        foreach ($index->getIndexes() as $code) {
            if ($index->isDisabledIndex($code) && $index->enableReindex($code)) {
                $index->forgetDisabledIndex($code);
                $enabledIndexes[] = $code;
            }
        }

        $executedIndexes = array();

        foreach ($enabledIndexes as $code) {
            if ($index->requireReindex($code) && $index->executeReindex($code)) {
                $executedIndexes[] = $code;
            }
        }

        if (empty($executedIndexes)) {
            return;
        }
    }

    //########################################
}
