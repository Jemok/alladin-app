<?php

class Boonagel_Alpesa_Model_Resource_Alpesaconfig extends Mage_Core_Model_Resource_Db_Abstract{

	protected function _construct(){
		$this->_init('alpesa/alpesaconfig','id');
	}

}
