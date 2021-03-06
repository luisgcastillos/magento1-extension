<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Resource_Order_Change
    extends Ess_M2ePro_Model_Resource_Abstract
{
    //########################################

    public function _construct()
    {
        $this->_init('M2ePro/Order_Change', 'id');
    }

    //########################################

    public function deleteByIds(array $ids)
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array(
                'id IN(?)' => $ids
            )
        );
    }

    public function deleteByOrderAction($orderId, $action)
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array(
                'order_id = ?' => $orderId,
                'action = ?' => $action
            )
        );
    }

    public function deleteByProcessingAttemptCount($count = 3, $component = null)
    {
        $count = (int)$count;

        if ($count <= 0) {
            return;
        }

        $where = array(
            'processing_attempt_count >= ?' => $count
        );

        if ($component !== null) {
            $where['component = ?'] = $component;
        }

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $where
        );
    }

    //########################################

    public function incrementAttemptCount(array $ids, $increment = 1)
    {
        $increment = (int)$increment;

        if ($increment <= 0) {
            return;
        }

        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            array(
                'processing_attempt_count' => new Zend_Db_Expr('processing_attempt_count + ' . $increment),
                'processing_attempt_date' => Mage::helper('M2ePro')->getCurrentGmtDate()
            ),
            array(
                'id IN (?)' => $ids
            )
        );
    }

    //########################################
}
