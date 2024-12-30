<?php

namespace Jvdh\SidebarStockFilter\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Product;
use Jvdh\SidebarStockFilter\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class LockAttributeForEdit
 *
 * Locks the `custom_filter_qty` attribute for editing when the event is triggered.
 */
class LockAttributeForEdit implements ObserverInterface
{
    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param Data $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $helper,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * Execute observer logic.
     *
     * Locks the custom attribute during the specified event.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            // Retrieve product from the event
            $event = $observer->getEvent();
            $product = $event->getProduct();

            // Validate the product object
            if (!$product instanceof Product) {
                $this->logger->warning('LockAttributeForEdit: Invalid product object.');
                return;
            }

            // Lock the custom attribute
            $product->lockAttribute($this->helper->getCustomFilterAttribute());
        } catch (\Exception $e) {
            // Log any exceptions
            $this->logger->error('LockAttributeForEdit: Error occurred - ' . $e->getMessage());
        }
    }
}
