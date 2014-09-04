<?php
/**
 * Created by PhpStorm.
 * User: saulo.desousa
 * Date: 4/29/14
 * Time: 12:33 PM
 */

class Censere_Renewableproduct_Block_Renewableprodfilter extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();

        $request = Mage::app()->getRequest();
        $renewableproductfilter = $this->getData('renewableprod');

        if (is_null($renewableproductfilter)) {

//            var_dump(
//                (string)
            $renewableproductfilter = Mage::getModel('renewableprod/product')
                                        ->getCollection()
                                        ->addFieldToSelect('*')
                                        ->addFieldToFilter('real_cust_id',array('eq'=>$request->getParam('custid')))
                                        ->setOrder('real_order_id', 'desc')
//                                        ->getSelect()
//            )
            ;

            Mage::log(get_class($renewableproductfilter));

            $this->setData('renewableprod', $renewableproductfilter);

                $this->setRenewableCustFilter($renewableproductfilter);
            Mage::log('You have reached the getRenewableCustFilter() Function');
        }
    }
    /*
     * This is where custom value are defined in order to override the default values on the ShowPerPage section within the Pager.php pagination module
     * */
    protected $_availableLimit1 = array(3=>3,6=>6,9=>9,12=>12,15=>15);
    /*
     * This refers to the getLimit function in order to override the default values from Page.php and use these Custom values instead
     * */
    protected $_limit1          = null;
    protected $_limitVarName1   = 'limit';

    /*
     * This sets and collects array custom value in order to replace default ShowPerPage values from Pager.php pagination module
     * */
    public function getAvailableLimit()
    {
        return $this->_availableLimit1;
    }
    public function setAvailableLimit(array $limits2)
    {
        $this->_availableLimit = $limits2;
    }
    /*
     * This appends custom values from the AvailableLimit onto the LimitVarName i.e. ?limit=10 or ?limit=50
     * */
    public function getLimitVarName()
    {
        return $this->_limitVarName1;
    }
    public function setLimitVarName($varName1)
    {
        $this->_limitVarName1 = $varName1;
        return $this;
    }

    /*
     * This gets the availablelimit array values and appends to the limitvarname('limit'), controls and returns default / custom values to the Pager.php pagination module
     * */
    public function getLimit()
    {
        if ($this->_limit1 !== null) {
            return $this->_limit1;
        }
        $limits2 = $this->getAvailableLimit();
        if ($limit1 = $this->getRequest()->getParam($this->getLimitVarName())) {
            if (isset($limits2[$limit1])) {
                return $limit1;
            }
        }
        $limits2 = array_keys($limits2);
        return $limits2[0];
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /*
         * This prepares the custom block data view into the pager view in order for the pager view to take presidency over this custom phtml file => renewableprod.list.index refers to the controller file and action within the block directory from the custom module appended to => .pager prefix which is hardcoded
         * */
        $pager = $this->getLayout()->createBlock('page/html_pager', 'renewableprod.renewableprodfilter.index.pager')
            ->setCollection($this->getRenewableCustFilter());
//
        /*
         * This gets the Custom Array defined within this file @ protected $_availableLimit1 = array(...) and displays a DropDown options to filter data size from
         * */
        $pager->setAvailableLimit($this->getAvailableLimit());

        /*
         * This gets the Data from the DB and sets the page size to be filtered and limited by the pagination
         * */
        $this->getRenewableCustFilter()->setPageSize($this->getLimit());

        $this->setChild('pager', $pager);

        $this->getRenewableCustFilter()->load();

        return $this;
    }

    public function getPagerHtmladmin()
    {
        return $this->getChildHtml('pager');
    }
}