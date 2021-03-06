<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

/**@method Ess_M2ePro_Model_Resource_ActiveRecord_ActiveRecordAbstract getResource() */

use Ess_M2ePro_Model_ActiveRecord_Relation_Amazon_Factory as AmazonFactory;
use Ess_M2ePro_Model_ActiveRecord_Relation_Ebay_Factory as EbayFactory;
use Ess_M2ePro_Model_ActiveRecord_Relation_Walmart_Factory as WalmartFactory;

abstract class Ess_M2ePro_Model_ActiveRecord_Relation_ChildAbstract
    extends Ess_M2ePro_Model_ActiveRecord_ActiveRecordAbstract
{
    /** @var Ess_M2ePro_Model_ActiveRecord_Relation_Factory */
    protected $_relationFactory;

    /** @var Ess_M2ePro_Model_ActiveRecord_Relation_Amazon_Factory */
    protected $_amazonRelationFactory;

    /** @var Ess_M2ePro_Model_ActiveRecord_Relation_Walmart_Factory */
    protected $_walmartRelationFactory;

    /** @var Ess_M2ePro_Model_ActiveRecord_Relation_Ebay_Factory */
    protected $_ebayRelationFactory;

    /** @var Ess_M2ePro_Model_ActiveRecord_Relation */
    protected $_relationObject;

    //########################################

    public function __construct()
    {
        parent::__construct();
        $this->_relationFactory        = Mage::getSingleton('M2ePro/ActiveRecord_Relation_Factory');
        $this->_amazonRelationFactory  = Mage::getSingleton('M2ePro/ActiveRecord_Relation_Amazon_Factory');
        $this->_walmartRelationFactory = Mage::getSingleton('M2ePro/ActiveRecord_Relation_Walmart_Factory');
        $this->_ebayRelationFactory    = Mage::getSingleton('M2ePro/ActiveRecord_Relation_Ebay_Factory');
    }

    //########################################

    abstract public function getComponentMode();

    //########################################

    /**
     * @return Ess_M2ePro_Model_ActiveRecord_Relation_Factory|Mage_Core_Model_Abstract
     */
    public function getRelationFactory()
    {
        return $this->_relationFactory;
    }

    /**
     * @return AmazonFactory|EbayFactory|WalmartFactory
     * @throws Ess_M2ePro_Model_Exception_Logic
     */
    public function getComponentRelationFactory()
    {
        if (null === $this->getComponentMode()) {
            throw new Ess_M2ePro_Model_Exception_Logic('`component_mode` is required');
        }

        if ($this->getComponentMode() === Ess_M2ePro_Helper_Component_Amazon::NICK) {
            return $this->_amazonRelationFactory;
        }

        if ($this->getComponentMode() === Ess_M2ePro_Helper_Component_Ebay::NICK) {
            return $this->_ebayRelationFactory;
        }

        if ($this->getComponentMode() === Ess_M2ePro_Helper_Component_Walmart::NICK) {
            return $this->_walmartRelationFactory;
        }

        throw new Ess_M2ePro_Model_Exception_Logic('Unknown component nick ' . $this->getComponentMode());
    }

    public function setRelation(Ess_M2ePro_Model_ActiveRecord_Relation $relationObject)
    {
        if ($this !== $relationObject->getChildObject()) {
            throw new Ess_M2ePro_Model_Exception_Logic('Wrong Relation Object.');
        }

        $this->_relationObject = $relationObject;
        return $this;
    }

    public function withRelation()
    {
        if (null === $this->_relationObject) {
            $this->_relationObject = $this->_relationFactory->getByChild($this);
        }

        return $this->_relationObject;
    }

    //########################################

    /**
     * @return Ess_M2ePro_Model_ActiveRecord_Relation_ParentAbstract
     */
    public function getParentObject()
    {
        return $this->withRelation()->getParentObject();
    }

    //########################################
}
