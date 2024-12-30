<?php

namespace Jvdh\SidebarStockFilter\Plugin\Catalog\Model\Layer;

use Magento\Catalog\Model\Layer\FilterList as CoreFilterList;
use Magento\Catalog\Model\Layer;
use Jvdh\SidebarStockFilter\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogSearch\Model\Layer\Filter\Category as CatalogSearchCategory;
use Magento\Catalog\Model\Layer\Filter\Category as CatalogCategory;
use Psr\Log\LoggerInterface;

/**
 * Class FilterList
 *
 * Modifies the layered navigation filter list to conditionally remove the `custom_filter_qty` filter.
 */
class FilterList
{
    protected const CATALOG_CATEGORY_FILTER = 'category';
    protected const CATALOG_SEARCH_CATEGORY_FILTER = 'catalogsearch_category';

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var array
     */
    protected $categoryFilterTypes = [
        self::CATALOG_SEARCH_CATEGORY_FILTER => CatalogSearchCategory::class,
        self::CATALOG_CATEGORY_FILTER        => CatalogCategory::class,
    ];

    /**
     * FilterList constructor.
     *
     * @param array $categoryFilters
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        array $categoryFilters,
        Data $helper,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->categoryFilterTypes = array_merge($this->categoryFilterTypes, $categoryFilters);
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Removes the `custom_filter_qty` filter if the module is disabled.
     *
     * @param CoreFilterList $subject
     * @param array $result
     * @param Layer $layer
     * @return array
     */
    public function afterGetFilters(CoreFilterList $subject, array $result, Layer $layer): array
    {
        // Check if the module is enabled for the current website
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        if ($this->helper->isEnabled($websiteId)) {
            return $result; // Module is enabled, no changes required
        }

        // Log that the filter is being removed due to module disablement
        $this->logger->info('SidebarStockFilter: Module is disabled. Removing `custom_filter_qty` filter.');

        // Iterate through the filters and remove the custom attribute filter
        foreach ($result as $idx => $filter) {
            if ($filter->getRequestVar() === Data::ATTRIBUTE) {
                unset($result[$idx]);
                $this->logger->debug(sprintf('Removed filter with request var: %s', Data::ATTRIBUTE));
                break; // Exit loop after removing the filter
            }
        }

        return $result;
    }
}
