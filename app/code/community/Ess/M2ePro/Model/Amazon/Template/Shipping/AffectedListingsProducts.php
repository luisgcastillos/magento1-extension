<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2017 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Template_Shipping_AffectedListingsProducts
    extends Ess_M2ePro_Model_Template_AffectedListingsProductsAbstract
{
    //########################################

    public function loadCollection(array $filters = array())
    {
        /** @var Ess_M2ePro_Model_Resource_Listing_Product_Collection $listingProductCollection */
        $listingProductCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing_Product');
        $listingProductCollection->addFieldToFilter('template_shipping_id', $this->_model->getId());

        if (!empty($filters['only_physical_units'])) {
            $listingProductCollection->addFieldToFilter('is_variation_parent', 0);
        }

        return $listingProductCollection;
    }

    //########################################
}
