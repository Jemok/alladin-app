<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookPixel
 * @author    Apptrian
 * @copyright Copyright (c) 2017 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 */
class Apptrian_FacebookPixel_Model_Config_Pixelid
    extends Mage_Core_Model_Config_Data
{
    public function _beforeSave()
    {
        $result = $this->validate();
        
        if ($result !== true) {
            Mage::throwException(implode("\n", $result));
        }
        
        return parent::_beforeSave();
    }
    
    public function validate()
    {
        $errors    = array();
        $helper    = Mage::helper('apptrian_facebookpixel');
        $value     = $this->getValue();
        $validator = Zend_Validate::is(
            $value,
            'Regex',
            array('pattern' => '/^[0-9]+$/')
        );
        
        if (!$validator) {
            $errors[] = $helper->__('Facebook Pixel ID is not valid.');
        }
        
        if (empty($errors)) {
            return true;
        }
        
        return $errors;
    }
}
