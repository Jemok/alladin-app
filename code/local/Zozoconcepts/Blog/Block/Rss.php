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
 * Blog RSS block
 *
 * @category    Zozoconcepts
 * @package     Zozoconcepts_Blog
 * @author      Zozoconcepts Hybrid
 */
class Zozoconcepts_Blog_Block_Rss
    extends Mage_Core_Block_Template {
    /**
     * RSS feeds for this block
     */
    protected $_feeds = array();
    /**
     * add a new feed
     * @access public
     * @param string $label
     * @param string $url
     * @param bool $prepare
     * @return Zozoconcepts_Blog_Block_Rss
     * @author Zozoconcepts Hybrid
     */
    public function addFeed($label, $url, $prepare = false) {
        $link = ($prepare ? $this->getUrl($url) : $url);
        $feed = new Varien_Object();
        $feed->setLabel($label);
        $feed->setUrl($link);
        $this->_feeds[$link] = $feed;
        return $this;
    }
    /**
     * get the current feeds
     * @access public
     * @return array()
     * @author Zozoconcepts Hybrid
     */
    public function getFeeds() {
        return $this->_feeds;
    }
}
