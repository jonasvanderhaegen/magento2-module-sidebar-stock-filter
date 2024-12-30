<?php

namespace Jvdh\SidebarStockFilter\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Jvdh\SidebarStockFilter\Helper\Data;
use Magento\Catalog\Model\ProductRepository;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateFilterStockValueByOrder
 *
 * Updates the `custom_filter_qty` attribute when an order is placed.
 */
class UpdateFilterStockValueByOrder implements ObserverInterface
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param Data $helper
     * @param ProductRepository $productRepository
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $helper,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Executes the observer logic.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            // Check if the module is enabled
            if (!$this->helper->isEnabled()) {
                $this->logger->info('UpdateFilterStockValueByOrder: Module is disabled.');
                return;
            }

            $order = $observer->getEvent()->getOrder();

            // Iterate over order items
            foreach ($order->getItems() as $item) {
                // Only process specific product types
                if (in_array($item->getProductType(), ['virtual', 'simple'], true)) {
                    // Load product by ID
                    $product = $this->productRepository->getById($item->getProductId());

                    // Validate product data
                    if (!$product || !$product->getId()) {
                        $this->logger->warning(
                            sprintf('Product ID %d could not be found or is invalid.', $item->getProductId())
                        );
                        continue;
                    }

                    // Update the custom attribute
                    $product->setCustomFilterQty($product->getQuantityAndStockStatus()['qty']);
                    $this->productRepository->save($product);

                    $this->logger->info(
                        sprintf(
                            'Updated custom_filter_qty for product ID %d with value %s.',
                            $item->getProductId(),
                            $product->getQuantityAndStockStatus()['qty']
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            // Log any exceptions
            $this->logger->error('Error updating custom_filter_qty: ' . $e->getMessage());
        }
    }
}
