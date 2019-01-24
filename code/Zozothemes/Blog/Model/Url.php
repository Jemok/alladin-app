<?php
/** 
  * Zozothemes.
  * 
  * NOTICE OF LICENSE
  * 
  * This source file is subject to the Zozothemes.com license that is
  * available through the world-wide-web at this URL:
  * http://www.zozothemes.com/license-agreement.html
  * 
  * DISCLAIMER
  * 
  * Do not edit or add to this file if you wish to upgrade this extension to newer
  * version in the future.
  * 
  * @category   Zozothemes
  * @package    Zozothemes_Blog
  * @copyright  Copyright (c) 2014 Zozothemes (http://www.zozothemes.com/)
  * @license    http://www.zozothemes.com/LICENSE-1.0.html
  */
namespace Zozothemes\Blog\Model;

/**
 * Blog url model
 */
class Url
{
    /**
     * Permalink Types
     */
    const PERMALINK_TYPE_DEFAULT = 'default';
    const PERMALINK_TYPE_SHORT = 'short';

    /**
     * Objects Types
     */
    const CONTROLLER_POST = 'post';
    const CONTROLLER_CATEGORY = 'category';
    const CONTROLLER_ARCHIVE = 'archive';
    const CONTROLLER_SEARCH = 'search';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_registry = $registry;
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve permalink type
     * @return string
     */
    public function getPermalinkType()
    {
        return $this->_getConfig('type');
    }

    /**
     * Retrieve route name by controller
     * @param  string  $controllerName
     * @param  boolean $skip
     * @return string || null
     */
    public function getRoute($controllerName = null, $skip = true)
    {
        if ($controllerName) {
            $controllerName .= '_';
        }

        if ($route = $this->_getConfig($controllerName.'route')) {
            return $route;
        } else {
            return $skip ? $controllerName : null;
        }
    }

    /**
     * Retrieve controller name by route
     * @param  string  $route
     * @param  boolean $skip
     * @return string || null
     */
    public function getControllerName($route, $skip = true)
    {
        foreach([
            self::CONTROLLER_POST,
            self::CONTROLLER_CATEGORY,
            self::CONTROLLER_ARCHIVE,
            self::CONTROLLER_SEARCH
        ] as $controllerName) {
            if ($this->getRoute($controllerName) == $route) {
                return $controllerName;
            }
        }

        return $skip ? $route : null;
    }

    /**
     * Retrieve blog base url
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_url->getUrl($this->getRoute());
    }

    /**
     * Retrieve blog page url
     * @param  string $identifier
     * @param  string $controllerName
     * @return string
     */
    public function getUrl($identifier, $controllerName)
    {
        return $this->_url->getUrl(
            $this->getUrlPath($identifier, $controllerName)
        );
    }

    /**
     * Retrieve blog url path
     * @param  string $identifier
     * @param  string $controllerName
     * @return string
     */
    public function getUrlPath($identifier, $controllerName)
    {
        if (is_object($identifier)) {
            $identifier = $identifier->getIdentifier();
        }

        switch ($this->getPermalinkType()) {
            case self::PERMALINK_TYPE_DEFAULT :
                return $this->getRoute() . '/' . $this->getRoute($controllerName) . '/' . $identifier;
            case self::PERMALINK_TYPE_SHORT :
                if ($controllerName == self::CONTROLLER_SEARCH) {
                    return $this->getRoute() . '/' . $this->getRoute($controllerName) . '/' . $identifier;
                } else {
                    return $this->getRoute() . '/' . $identifier;
                }
        }
    }

    /**
     * Retrieve media url
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $file;
    }

    /**
     * Retrieve blog permalink config value
     * @param  string $key
     * @return string || null || int
     */
    protected function _getConfig($key)
    {
        return $this->_scopeConfig->getValue(
            'zozoblog/permalink/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}