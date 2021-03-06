<?php
/**
 * Zozoconcepts_Brands extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Zozoconcepts
 * @package        Zozoconcepts_Brands
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Brand tab on product edit form
 *
 * @category    Zozoconcepts
 * @package     Zozoconcepts_Brands
 * @author      Zozoconcepts Hybrid
 */
class Zozoconcepts_Brands_Block_Adminhtml_Catalog_Product_Edit_Tab_Brand
    extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * Set grid params
     * @access public
     * @author Zozoconcepts Hybrid
     */
    public function __construct() {
        parent::__construct();
        $this->setId('brand_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProduct()->getId()) {
            $this->setDefaultFilter(array('in_brands'=>1));
        }
    }
    /**
     * prepare the brand collection
     * @access protected
     * @return Zozoconcepts_Brands_Block_Adminhtml_Catalog_Product_Edit_Tab_Brand
     * @author Zozoconcepts Hybrid
     */
    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('zozoconcepts_brands/brand_collection');
        if ($this->getProduct()->getId()){
            $constraint = 'related.product_id='.$this->getProduct()->getId();
            }
            else{
                $constraint = 'related.product_id=0';
            }
        $collection->getSelect()->joinLeft(
            array('related'=>$collection->getTable('zozoconcepts_brands/brand_product')),
            'related.brand_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    /**
     * prepare mass action grid
     * @access protected
     * @return Zozoconcepts_Brands_Block_Adminhtml_Catalog_Product_Edit_Tab_Brand
     * @author Zozoconcepts Hybrid
     */
    protected function _prepareMassaction(){
        return $this;
    }
    /**
     * prepare the grid columns
     * @access protected
     * @return Zozoconcepts_Brands_Block_Adminhtml_Catalog_Product_Edit_Tab_Brand
     * @author Zozoconcepts Hybrid
     */
    protected function _prepareColumns(){
        $this->addColumn('in_brands', array(
            'header_css_class'  => 'a-center',
            'type'  => 'checkbox',
            'name'  => 'in_brands',
            'values'=> $this->_getSelectedBrands(),
            'align' => 'center',
            'index' => 'entity_id'
        ));
        $this->addColumn('title', array(
            'header'=> Mage::helper('zozoconcepts_brands')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));
        $this->addColumn('position', array(
            'header'        => Mage::helper('zozoconcepts_brands')->__('Position'),
            'name'          => 'position',
            'width'         => 60,
            'type'        => 'number',
            'validate_class'=> 'validate-number',
            'index'         => 'position',
            'editable'      => true,
        ));
        return parent::_prepareColumns();
    }
    /**
     * Retrieve selected brands
     * @access protected
     * @return array
     * @author Zozoconcepts Hybrid
     */
    protected function _getSelectedBrands(){
        $brands = $this->getProductBrands();
        if (!is_array($brands)) {
            $brands = array_keys($this->getSelectedBrands());
        }
        return $brands;
    }
     /**
     * Retrieve selected brands
     * @access protected
     * @return array
     * @author Zozoconcepts Hybrid
     */
    public function getSelectedBrands() {
        $brands = array();
        //used helper here in order not to override the product model
        $selected = Mage::helper('zozoconcepts_brands/product')->getSelectedBrands(Mage::registry('current_product'));
        if (!is_array($selected)){
            $selected = array();
        }
        foreach ($selected as $brand) {
            $brands[$brand->getId()] = array('position' => $brand->getPosition());
        }
        return $brands;
    }
    /**
     * get row url
     * @access public
     * @param Zozoconcepts_Brands_Model_Brand
     * @return string
     * @author Zozoconcepts Hybrid
     */
    public function getRowUrl($item){
        return '#';
    }
    /**
     * get grid url
     * @access public
     * @return string
     * @author Zozoconcepts Hybrid
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/brandsGrid', array(
            'id'=>$this->getProduct()->getId()
        ));
    }
    /**
     * get the current product
     * @access public
     * @return Mage_Catalog_Model_Product
     * @author Zozoconcepts Hybrid
     */
    public function getProduct(){
        return Mage::registry('current_product');
    }
    /**
     * Add filter
     * @access protected
     * @param object $column
     * @return Zozoconcepts_Brands_Block_Adminhtml_Catalog_Product_Edit_Tab_Brand
     * @author Zozoconcepts Hybrid
     */
    protected function _addColumnFilterToCollection($column){
        if ($column->getId() == 'in_brands') {
            $brandIds = $this->_getSelectedBrands();
            if (empty($brandIds)) {
                $brandIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$brandIds));
            }
            else {
                if($brandIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$brandIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
