<?php
/**
 * Zozoconcepts_Blog extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Zozoconcepts
 * @package        Zozoconcepts_Blog
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Blog admin edit form
 *
 * @category    Zozoconcepts
 * @package     Zozoconcepts_Blog
 * @author      Zozoconcepts Hybrid
 */
class Zozoconcepts_Blog_Block_Adminhtml_Blog_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * constructor
     * @access public
     * @return void
     * @author Zozoconcepts Hybrid
     */
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'zozoconcepts_blog';
        $this->_controller = 'adminhtml_blog';
        $this->_updateButton('save', 'label', Mage::helper('zozoconcepts_blog')->__('Save Post'));
        $this->_updateButton('delete', 'label', Mage::helper('zozoconcepts_blog')->__('Delete Post'));
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('zozoconcepts_blog')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    /**
     * get the edit form header
     * @access public
     * @return string
     * @author Zozoconcepts Hybrid
     */
    public function getHeaderText(){
        if( Mage::registry('current_blog') && Mage::registry('current_blog')->getId() ) {
            return Mage::helper('zozoconcepts_blog')->__("Edit Post '%s'", $this->escapeHtml(Mage::registry('current_blog')->getTitle()));
        }
        else {
            return Mage::helper('zozoconcepts_blog')->__('Add Post');
        }
    }
}
