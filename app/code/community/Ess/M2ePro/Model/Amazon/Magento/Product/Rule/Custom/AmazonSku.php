<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Magento_Product_Rule_Custom_AmazonSku
    extends Ess_M2ePro_Model_Magento_Product_Rule_Custom_Abstract
{
    //########################################

    /**
     * @return string
     */
    public function getAttributeCode()
    {
        return 'sku';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('M2ePro')->__('SKU');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return mixed
     */
    public function getValueByProductInstance(Mage_Catalog_Model_Product $product)
    {
        return $product->getData('sku');
    }

    //########################################
}
