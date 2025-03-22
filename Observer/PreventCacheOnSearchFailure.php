<?php

namespace Mercurieus\SearchFpcProtection\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;
use Mercurieus\SearchFpcProtection\Model\SearchEngineHealthCheck;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Prevents category pages from being cached when search engine is unavailable
 */
class PreventCacheOnSearchFailure implements ObserverInterface
{
    /**
     * Configuration path for feature enablement
     */
    private const XML_PATH_ENABLED = 'catalog/search/disable_cache_on_failure';

    /**
     * Layout handle that identifies category pages
     */
    private const CATEGORY_PAGE_HANDLE = 'catalog_category_view';

    private SearchEngineHealthCheck $searchEngineHealthCheck;
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param SearchEngineHealthCheck $searchEngineHealthCheck Service to check search engine availability
     * @param ScopeConfigInterface $scopeConfig Access to system configuration
     */
    public function __construct(
        SearchEngineHealthCheck $searchEngineHealthCheck,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->searchEngineHealthCheck = $searchEngineHealthCheck;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Disables page caching for category pages when search engine is down
     *
     * Checks:
     * 1. If the feature is enabled in configuration
     * 2. If current page is a category page
     * 3. If search engine is available
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $layout = $observer->getEvent()->getLayout();

        // Check if current page is category page and search engine is down
        if ($this->isCategoryPage($layout) && !$this->searchEngineHealthCheck->isSearchEngineAvailable()) {
            $layout->getUpdate()->addHandle('page_cache_disabled');
        }
    }

    /**
     * Checks if the feature is enabled in system configuration
     *
     * @return bool
     */
    private function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Determines if current page is a category page
     *
     * @param Layout $layout
     * @return bool
     */
    private function isCategoryPage(Layout $layout): bool
    {
        $handles = $layout->getUpdate()->getHandles();
        return !empty($handles) && in_array(self::CATEGORY_PAGE_HANDLE, $handles);
    }
}
