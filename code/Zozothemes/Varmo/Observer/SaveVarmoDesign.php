<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zozothemes\Varmo\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveVarmoDesign implements ObserverInterface
{
    protected $_messageManager;
    protected $_cssGenerator;
    
    /**
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Zozothemes\Varmo\Model\Cssconfig\Generator $cssenerator,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_cssGenerator = $cssenerator;
        $this->_messageManager = $messageManager;
    }

    /**
     * Log out user and redirect to new admin custom url
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $message = 'Saved Varmo Design...';
        $this->_cssGenerator->generateCss('design', $observer->getData("website"), $observer->getData("store"));
    }
}
