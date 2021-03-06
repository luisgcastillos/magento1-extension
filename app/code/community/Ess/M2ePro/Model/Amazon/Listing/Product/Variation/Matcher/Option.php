<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Listing_Product_Variation_Matcher_Option
{
    /** @var Ess_M2ePro_Model_Magento_Product $_magentoProduct */
    protected $_magentoProduct = null;

    protected $_destinationOptions = array();

    protected $_destinationOptionsLocalVocabularyNames = array();

    protected $_destinationOptionsServerVocabularyNames = array();

    protected $_matchedAttributes = array();

    /** @var Ess_M2ePro_Model_Amazon_Listing_Product_Variation_Matcher_Option_Resolver $_resolver */
    protected $_resolver = null;

    //########################################

    /**
     * @param Ess_M2ePro_Model_Magento_Product $magentoProduct
     * @return $this
     */
    public function setMagentoProduct(Ess_M2ePro_Model_Magento_Product $magentoProduct)
    {
        $this->_magentoProduct = $magentoProduct;
        return $this;
    }

    // ---------------------------------------

    /**
     * $destinationOptions = array(
     *       'B00005N5PF' => array(
     *         'Color' => 'Red',
     *         'Size'  => 'XL',
     *       ),
     *       ...
     *   )
     *
     * @param array $destinationOptions
     * @return $this
     */
    public function setDestinationOptions(array $destinationOptions)
    {
        $this->_destinationOptions = $destinationOptions;

        $this->_destinationOptionsLocalVocabularyNames  = array();
        $this->_destinationOptionsServerVocabularyNames = array();

        return $this;
    }

    /**
     * @param array $matchedAttributes
     * @return $this
     */
    public function setMatchedAttributes(array $matchedAttributes)
    {
        $this->_matchedAttributes = $matchedAttributes;
        return $this;
    }

    //########################################

//    $sourceOption = array(
//         'Color' => 'red',
//         'Size'  => 'L'
//    )

    /**
     * @param array $sourceOption
     * @return null|int
     * @throws Ess_M2ePro_Model_Exception
     */
    public function getMatchedOptionGeneralId(array $sourceOption)
    {
        $this->validate();

        if ($generalId = $this->matchGeneralIdByNames($sourceOption)) {
            return $generalId;
        }

        if ($generalId = $this->matchGeneralIdByLocalVocabulary($sourceOption)) {
            return $generalId;
        }

        if ($generalId = $this->matchGeneralIdByServerVocabulary($sourceOption)) {
            return $generalId;
        }

        return null;
    }

    //########################################

    protected function validate()
    {
        if ($this->_magentoProduct === null) {
            throw new Ess_M2ePro_Model_Exception('Magento Product was not set.');
        }

        if (empty($this->_destinationOptions)) {
            throw new Ess_M2ePro_Model_Exception('Destination Options is empty.');
        }
    }

    // ---------------------------------------

    protected function matchGeneralIdByNames(array $sourceOption)
    {
        $sourceOptionNames = array();
        foreach ($sourceOption as $attribute => $option) {
            $sourceOptionNames[$attribute] = $this->prepareOptionNames($option);
        }

        $this->getResolver()
            ->setSourceOption($sourceOptionNames)
            ->setDestinationOptions($this->_destinationOptions)
            ->setMatchedAttributes($this->_matchedAttributes);

        return $this->getResolver()->resolve()->getResolvedGeneralId();
    }

    protected function matchGeneralIdByLocalVocabulary(array $sourceOption)
    {
        $this->getResolver()
            ->setSourceOption($this->getSourceOptionNames($sourceOption))
            ->setDestinationOptions($this->getDestinationOptionLocalVocabularyNames())
            ->setMatchedAttributes($this->_matchedAttributes);

        return $this->getResolver()->resolve()->getResolvedGeneralId();
    }

    protected function matchGeneralIdByServerVocabulary(array $sourceOption)
    {
        $this->getResolver()
            ->setSourceOption($this->getSourceOptionNames($sourceOption))
            ->setDestinationOptions($this->getDestinationOptionServerVocabularyNames())
            ->setMatchedAttributes($this->_matchedAttributes);

        return $this->getResolver()->resolve()->getResolvedGeneralId();
    }

    // ---------------------------------------

    protected function getSourceOptionNames($sourceOption)
    {
        $magentoOptionNames = $this->_magentoProduct->getVariationInstance()->getTitlesVariationSet();

        $resultNames = array();
        foreach ($sourceOption as $attribute => $option) {
            $names = array();
            if (isset($magentoOptionNames[$attribute]['values'])) {
                $attributeValues = $magentoOptionNames[$attribute]['values'];
                foreach ($attributeValues as $defaultValue => $optionValues) {
                    if (in_array($option, $optionValues, true)) {
                        $names = $magentoOptionNames[$attribute]['values'][$defaultValue];
                    }
                }
            }

            $resultNames[$attribute] = $this->prepareOptionNames($option, $names);
        }

        return $resultNames;
    }

    protected function getDestinationOptionLocalVocabularyNames()
    {
        if (!empty($this->_destinationOptionsLocalVocabularyNames)) {
            return $this->_destinationOptionsLocalVocabularyNames;
        }

        $vocabularyHelper = Mage::helper('M2ePro/Component_Amazon_Vocabulary');

        foreach ($this->_destinationOptions as $generalId => $destinationOption) {
            foreach ($destinationOption as $attributeName => $optionName) {
                $this->_destinationOptionsLocalVocabularyNames[$generalId][$attributeName] = $this->prepareOptionNames(
                    $optionName, $vocabularyHelper->getLocalOptionNames($attributeName, $optionName)
                );
            }
        }

        return $this->_destinationOptionsLocalVocabularyNames;
    }

    protected function getDestinationOptionServerVocabularyNames()
    {
        if (!empty($this->_destinationOptionsServerVocabularyNames)) {
            return $this->_destinationOptionsServerVocabularyNames;
        }

        $vocabularyHelper = Mage::helper('M2ePro/Component_Amazon_Vocabulary');

        foreach ($this->_destinationOptions as $generalId => $destinationOption) {
            foreach ($destinationOption as $attributeName => $optionName) {
                $this->_destinationOptionsServerVocabularyNames[$generalId][$attributeName] = $this->prepareOptionNames(
                    $optionName, $vocabularyHelper->getServerOptionNames($attributeName, $optionName)
                );
            }
        }

        return $this->_destinationOptionsServerVocabularyNames;
    }

    //########################################

    protected function getResolver()
    {
        if ($this->_resolver !== null) {
            return $this->_resolver;
        }

        $this->_resolver = Mage::getModel('M2ePro/Amazon_Listing_Product_Variation_Matcher_Option_Resolver');
        return $this->_resolver;
    }

    protected function prepareOptionNames($option, array $names = array())
    {
        $names[] = $option;
        $names = array_unique($names);

        $names = array_map('trim', $names);
        $names = array_map('strtolower', $names);

        return $names;
    }

    //########################################
}
