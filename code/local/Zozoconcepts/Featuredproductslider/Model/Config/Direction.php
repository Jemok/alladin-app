<?php
/**
 * Zozoconcepts_Featuredproductslider extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Zozoconcepts
 * @package        Zozoconcepts_Popup
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Popup block
 *
 * @category    Zozoconcepts
 * @package     Zozoconcepts_Featuredproductslider
 * @author      Zozoconcepts Hybrid
 */
class Zozoconcepts_Featuredproductslider_Model_Config_Direction
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'asc', 'label'=>Mage::helper('adminhtml')->__('Ascending')),
            array('value'=>'desc', 'label'=>Mage::helper('adminhtml')->__('Descending'))
        );
    }

}
