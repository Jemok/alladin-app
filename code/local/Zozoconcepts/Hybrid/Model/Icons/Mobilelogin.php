<?php
/**
 * Zozoconcepts_Hybrid extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Zozoconcepts
 * @package        Zozoconcepts_Hybrid
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * 
 *
 * @category    Zozoconcepts
 * @package     Zozoconcepts_Hybrid
 * @author      Zozoconcepts Hybrid
 */
class Zozoconcepts_Hybrid_Model_Icons_Mobilelogin
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fa-lock', 'label'=>Mage::helper('hybrid')->__('fa-lock')),
            array('value'=>'fa-sign-in', 'label'=>Mage::helper('hybrid')->__('fa-sign-in')),
            array('value'=>'fa-power-off', 'label'=>Mage::helper('hybrid')->__('fa-power-off')),
            array('value'=>'fa-check-square-o', 'label'=>Mage::helper('hybrid')->__('fa-check-square-o')),
            array('value'=>'fa-user', 'label'=>Mage::helper('hybrid')->__('fa-user')),
            array('value'=>'fa-toggle-on', 'label'=>Mage::helper('hybrid')->__('fa-toggle-on'))
        );
    }

}