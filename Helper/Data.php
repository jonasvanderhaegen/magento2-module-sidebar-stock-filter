<?php

namespace Jvdh\SidebarStockFilter\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Data
 *
 * Provides helper methods for the Sidebar Stock Filter module, including
 * configuration checks and attribute references.
 */
class Data extends AbstractHelper
{
    /**
     * Constant for the custom stock filter attribute.
     */
    public const ATTRIBUTE = 'custom_filter_qty';

    /**
     * Checks if the Sidebar Stock Filter module is enabled for the given website.
     *
     * @param int|null $websiteId The website ID to check. Defaults to null for the current website.
     * @return bool True if the module is enabled, otherwise false.
     */
    public function isEnabled(?int $websiteId = null): bool
    {
        try {
            // Check if the module is enabled for the given website scope
            return $this->scopeConfig->isSetFlag(
                'jvdhstockfilter/general/enabled',
                ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            );
        } catch (\Exception $e) {
            // Log error and return false to ensure predictable behavior
            $this->_logger->error(__('Error checking module status: %1', $e->getMessage()));
            return false;
        }
    }

    /**
     * Retrieves the custom attribute name for filtering products by stock quantity.
     *
     * @return string The custom attribute code.
     */
    public function getCustomFilterAttribute(): string
    {
        return self::ATTRIBUTE;
    }
}
