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
 * Router
 *
 * @category    Zozoconcepts
 * @package     Zozoconcepts_Blog
 * @author      Zozoconcepts Hybrid
 */
class Zozoconcepts_Blog_Controller_Router
    extends Mage_Core_Controller_Varien_Router_Abstract {
    /**
     * init routes
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Zozoconcepts_Blog_Controller_Router
     * @author Zozoconcepts Hybrid
     */
    public function initControllerRouters($observer){
        $front = $observer->getEvent()->getFront();
        $front->addRouter('zozoconcepts_blog', $this);
        return $this;
    }
    /**
     * Validate and match entities and modify request
     * @access public
     * @param Zend_Controller_Request_Http $request
     * @return bool
     * @author Zozoconcepts Hybrid
     */
    public function match(Zend_Controller_Request_Http $request){
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $urlKey = trim($request->getPathInfo(), '/');
        $check = array();
        $check['blog'] = new Varien_Object(array(
            'prefix'        => Mage::getStoreConfig('zozoconcepts_blog/blog/url_prefix'),
            'suffix'        => Mage::getStoreConfig('zozoconcepts_blog/blog/url_suffix'),
            'model'         =>'zozoconcepts_blog/blog',
            'controller'    => 'blog',
            'action'        => 'view',
            'param'         => 'id',
            'check_path'    => 0
        ));
        $check['category'] = new Varien_Object(array(
            'prefix'        => Mage::getStoreConfig('zozoconcepts_blog/category/url_prefix'),
            'suffix'        => Mage::getStoreConfig('zozoconcepts_blog/category/url_suffix'),
            'model'         =>'zozoconcepts_blog/category',
            'controller'    => 'category',
            'action'        => 'view',
            'param'         => 'id',
            'check_path'    => 0
        ));
        foreach ($check as $key=>$settings) {
            if ($settings['prefix']){
                $parts = explode('/', $urlKey);
                if ($parts[0] != $settings['prefix'] || count($parts) != 2){
                    continue;
                }
                $urlKey = $parts[1];
            }
            if ($settings['suffix']){
                $urlKey = substr($urlKey, 0 , -strlen($settings['suffix']) - 1);
            }
            $model = Mage::getModel($settings->getModel());
            $id = $model->checkUrlKey($urlKey, Mage::app()->getStore()->getId());
            if ($id){
                if ($settings->getCheckPath() && !$model->load($id)->getStatusPath()) {
                    continue;
                }
                $request->setModuleName('blog')
                    ->setControllerName($settings->getController())
                    ->setActionName($settings->getAction())
                    ->setParam($settings->getParam(), $id);
                $request->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    $urlKey
                );
                return true;
            }
        }
        return false;
    }
}
