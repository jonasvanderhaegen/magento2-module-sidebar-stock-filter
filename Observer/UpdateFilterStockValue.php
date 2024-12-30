<?php

namespace Jvdh\SidebarStockFilter\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Model\ProductRepository;
use Jvdh\SidebarStockFilter\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateFilterStockValue
 *
 * Updates the `custom_filter_qty` attribute whenever a product's stock is updated.
 */
class UpdateFilterStockValue implements ObserverInterface
{
    /**
     * @var ProductAction
     */
    protected ProductAction $productAction;

    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param ProductAction $productAction
     * @param ProductRepository $productRepository
     * @param Data $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductAction $productAction,
        ProductRepository $productRepository,
        Data $helper,
        LoggerInterface $logger
    ) {
        $this->productAction = $productAction;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
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
            // Retrieve stock item from the event
            $item = $observer->getEvent()->getItem();

            // Validate the item and its data
            if (!$item || !$item->getProductId() || $item->getQty() === null) {
                $this->logger->warning('UpdateFilterStockValue: Invalid stock item or missing data.');
                return;
            }

            // Check if the module is enabled
            if (!$this->helper->isEnabled()) {
                $this->logger->info('UpdateFilterStockValue: Module is disabled. Skipping attribute update.');
                return;
            }

            // Update the custom attribute with the stock quantity
            $this->productAction->updateAttributes(
                [$item->getProductId()],
                [$this->helper::ATTRIBUTE => $item->getQty()],
                0
            );

            $this->logger->info(
                sprintf(
                    'UpdateFilterStockValue: Updated %s for product ID %d with value %d.',
                    $this->helper::ATTRIBUTE,
                    $item->getProductId(),
                    $item->getQty()
                )
            );
        } catch (\Exception $e) {
            // Log any errors that occur during the update process
            $this->logger->error('UpdateFilterStockValue: Error updating attribute - ' . $e->getMessage());
        }
    }
}
