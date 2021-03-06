<?php

class Mss_Bannersliderapp_BannerController extends Mage_Core_Controller_Front_Action {

	private $key = 'bannerslider';
	private $store = '1';

	public function _construct(){

		header('content-type: application/json; charset=utf-8');
		header("access-control-allow-origin: *");
		
		Mage::helper('connector')->loadParent(Mage::app()->getFrontController()->getRequest()->getHeader('token'));
		parent::_construct();
		
	}
	
	public function bannerAction(){
		$bannerCollection = Mage::getModel('banners/banners')->getCollection();
		$bannerCollection->addFieldToFilter( 'status', '1' );


// echo '<pre>';
// print_r($bannerCollection->getData());
// die();


			/*check for cache*/
			Mage::helper('connector')->checkcache($this->key,$this->store);
			
			$alldata = array(); 
			foreach($bannerCollection->getData() as $bannerdata){
				 				
					//$path = Mage::helper('bannersliderapp')->reImageName( $bannerdata['image']);

					$path = $bannerdata['bannerimage'];

					$imgeurl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/'.$path;
					
					$data['name'] = $bannerdata['title'];
					
					$data['image_description'] = $bannerdata['content'];
					$data['image_url'] = $imgeurl;
					$data['category_name'] = Mage::getModel('catalog/category')->load($bannerdata['category_id'])->getName();
					$data['link_type'] = $bannerdata['url_type'];
					$data['product_id'] = $bannerdata['product_id'];
					$data['category_id'] = $bannerdata['category_id'];
					$data['check_type'] = $bannerdata['check_type'];
						
                    $alldata[] = $data; 
			
			}
			if(Mage::getStoreConfig('mss/connector/email') != '1'):
			    $event_data_array  =  array(true);
				Mage::dispatchEvent('send_email_store_data', $event_data_array);
			endif;

			if(sizeof($alldata)):
				/*create new cache*/
				Mage::helper('connector')->createNewcache($this->key,$this->store,json_encode(array('status'=>'success','data'=>$alldata)));
				echo json_encode(array('status'=>'success','data'=>$alldata));
				exit;
			else:
				echo json_encode(array('status'=>'error','message'=> $this->__('No banner uploaded')));
			endif;
	}
}

?>