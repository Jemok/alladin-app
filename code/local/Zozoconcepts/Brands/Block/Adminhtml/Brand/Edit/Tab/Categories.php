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
 * brand - categories relation edit block
 *
 * @category    Zozoconcepts
 * @package     Zozoconcepts_Brands
 * @author      Zozoconcepts Hybrid
 */
class Zozoconcepts_Brands_Block_Adminhtml_Brand_Edit_Tab_Categories
    extends Mage_Adminhtml_Block_Catalog_Category_Tree {
    protected $_categoryIds = null;
    protected $_selectedNodes = null;

    /**
     * constructor
     * Specify template to use
     * @access public
     * @author Zozoconcepts Hybrid
     */
    public function __construct() {
        parent::__construct();
        $this->setTemplate('zozoconcepts_brands/brand/edit/tab/categories.phtml');
        $this->_withProductCount = false;
    }
    /**
     * Retrieve currently edited brand
     * @access public
     * @return Zozoconcepts_Brands_Model_Brand
     * @author Zozoconcepts Hybrid
     */
    public function getBrand(){
        return Mage::registry('current_brand');
    }
    /**
     * Return array with categories IDs which the brand is linked to
     * @access public
     * @return array
     * @author Zozoconcepts Hybrid
     */
    public function getCategoryIds(){
        if (is_null($this->_categoryIds)){
            $categories = $this->getBrand()->getSelectedCategories();
                $ids = array();
                foreach ($categories as $category){
                    $ids[] = $category->getId();
                }
                $this->_categoryIds = $ids;
        }
        return $this->_categoryIds;
    }
    /**
     * Forms string out of getCategoryIds()
     * @access public
     * @return string
     * @author Zozoconcepts Hybrid
     */
    public function getIdsString(){
        return implode(',', $this->getCategoryIds());
    }
    /**
     * Returns root node and sets 'checked' flag (if necessary)
     * @access public
     * @return Varien_Data_Tree_Node
     * @author Zozoconcepts Hybrid
     */
    public function getRootNode(){
        $root = $this->getRoot();
        if ($root && in_array($root->getId(), $this->getCategoryIds())) {
            $root->setChecked(true);
        }
        return $root;
    }

    /**
     * Returns root node
     *
     * @param Zozoconcepts_Brands_Model_Category|null $parentNodeCategory
     * @param int  $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Zozoconcepts Hybrid
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3){
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('category_root');
        if (is_null($root)) {
            $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            $ids = $this->getSelectedCategoryPathIds($rootId);
            $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->loadByIds($ids, false, false);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            $root = $tree->getNodeById($rootId);
            Mage::register('category_root', $root);
        }
        return $root;
    }
    /**
     * Returns array with configuration of current node
     * @access public
     * @param Varien_Data_Tree_Node $node
     * @param int $level How deep is the node in the tree
     * @return array
     * @author Zozoconcepts Hybrid
     */
    protected function _getNodeJson($node, $level = 1){
        $item = parent::_getNodeJson($node, $level);
        if ($this->_isParentSelectedCategory($node)) {
            $item['expanded'] = true;
        }
        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }
        return $item;
    }
    /**
     * Returns whether $node is a parent (not exactly direct) of a selected node
     * @access public
     * @param Varien_Data_Tree_Node $node
     * @return bool
     * @author Zozoconcepts Hybrid
     */
    protected function _isParentSelectedCategory($node){
        $result = false;
        // Contains string with all category IDs of children (not exactly direct) of the node
        $allChildren = $node->getAllChildren();
        if ($allChildren) {
            $selectedCategoryIds = $this->getCategoryIds();
            $allChildrenArr = explode(',', $allChildren);
            for ($i = 0, $cnt = count($selectedCategoryIds); $i < $cnt; $i++) {
                $isSelf = $node->getId() == $selectedCategoryIds[$i];
                if (!$isSelf && in_array($selectedCategoryIds[$i], $allChildrenArr)) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }
    /**
     * Returns array with nodes those are selected (contain current brand)
     *
     * @return array
     */
    protected function _getSelectedNodes(){
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            $root = $this->getRoot();
            foreach ($this->getCategoryIds() as $categoryId) {
                if ($root) {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($categoryId);
                }
            }
        }
        return $this->_selectedNodes;
    }

    /**
     * Returns JSON-encoded array of category children
     * @access public
     * @param int $categoryId
     * @return string
     * @author Zozoconcepts Hybrid
     */
    public function getCategoryChildrenJson($categoryId){
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $node = $this->getRoot($category, 1)->getTree()->getNodeById($categoryId);
        if (!$node || !$node->hasChildren()) {
            return '[]';
        }
        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }
        return Mage::helper('core')->jsonEncode($children);
    }
    /**
     * Returns URL for loading tree
     * @access public
     * @param null $expanded
     * @return string
     * @author Zozoconcepts Hybrid
     */
    public function getLoadTreeUrl($expanded = null){
        return $this->getUrl('*/*/categoriesJson', array('_current' => true));
    }

    /**
     * Return distinct path ids of selected category
     * @access public
     * @param mixed $rootId Root category Id for context
     * @return array
     * @author Zozoconcepts Hybrid
     */
    public function getSelectedCategoryPathIds($rootId = false){
        $ids = array();
        $categoryIds = $this->getCategoryIds();
        if (empty($categoryIds)) {
            return array();
        }
        $collection = Mage::getResourceModel('catalog/category_collection');
        if ($rootId) {
            $collection->addFieldToFilter('parent_id', $rootId);
        }
        else {
            $collection->addFieldToFilter('entity_id', array('in'=>$categoryIds));
        }

        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }
}