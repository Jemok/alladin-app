<?php
/**
 * Zozoconcepts_Socio extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Zozoconcepts
 * @package        Zozoconcepts_Socio
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * 
 *
 * @category    Zozoconcepts
 * @package     Zozoconcepts_Socio
 * @author      Zozoconcepts Hybrid
 */
class Zozoconcepts_Socio_Model_Feed extends Mage_Core_Model_Abstract
{	
	const RESPONSE_LIMIT =5;
	public function getFeed($username, $limit = self::RESPONSE_LIMIT){
		try{
			require_once('twitteroauth/TwitterAPIExchange.php');
			$coreHelper = Mage::helper('core');
			$helper = Mage::helper('zozoconcepts_socio');
			$username = trim($username);
			if($helper->getLimit()){ 
				$limit = $helper->getLimit(); 
			}
			$limit = intval($limit);		
			$tweets = array();
			 
			if (!$username || !$limit || $limit <= 0) {
			return array();
			}		 
			
			// this is where we instantiate and configure
			// our API interface.
			$twitter = new TwitterAPIExchange(array(
			'oauth_access_token' => $helper->getAccessToken(),
			'oauth_access_token_secret' => $helper->getAccessTokenSecret(),
			'consumer_key' => $helper->getConsumerKey(),
			'consumer_secret' => $helper->getConsumerSecrete()
			));
			 
			// set our parameters for accessing user_timeline.json
			// see more: https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
			$getParameters = array(
			'screen_name=' . urlencode($username),
			'count=' . urlencode($limit)
			);
			 
			// this is where the request is being made
			$responseJson = $twitter->setGetfield('?' . implode('&', $getParameters))
			->buildOauth('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET')
			->performRequest();
			 
			// decode JSON response
			$response = $coreHelper->jsonDecode($responseJson);
			 
			if (isset($response['statuses'])) {
			$response = $response['statuses'];
			}
			 
			if (empty($response)) {
			return array();
			}
			return $response;
		}catch(Exception $e){
			return array();
		}
	}
}
