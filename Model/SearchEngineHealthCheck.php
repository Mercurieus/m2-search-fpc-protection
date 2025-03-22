<?php

namespace Mercurieus\SearchFpcProtection\Model;

use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\Framework\Search\EngineResolverInterface;
use Psr\Log\LoggerInterface;

/**
 * Checks search engine health status
 * Used to determine if search functionality is available
 */
class SearchEngineHealthCheck
{
    /**
     * List of search engines that require connection health check
     * Other engines will be considered always available
     */
    private const SUPPORTED_ENGINES = [
        'elasticsearch7',
        'opensearch'
    ];

    private LoggerInterface $logger;
    private EngineResolverInterface $engineResolver;
    private ConnectionManager $connectionManager;

    /**
     * @param EngineResolverInterface $engineResolver Determines current search engine
     * @param ConnectionManager $connectionManager Manages search engine connection
     * @param LoggerInterface $logger Logs connection errors
     */
    public function __construct(
        EngineResolverInterface $engineResolver,
        ConnectionManager $connectionManager,
        LoggerInterface $logger
    ) {
        $this->engineResolver = $engineResolver;
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    /**
     * Checks if current search engine is available
     *
     * Returns true in following cases:
     * - Current engine is not in supported list
     * - Current engine connection test succeeds
     *
     * Returns false if connection test fails
     *
     * @return bool
     */
    public function isSearchEngineAvailable(): bool
    {
        $engine = $this->engineResolver->getCurrentSearchEngine();

        // Skip health check for unsupported engines
        if (!$this->isSupportedEngine($engine)) {
            return true;
        }

        try {
            // Test actual connection to search engine
            return $this->connectionManager->getConnection()->testConnection();
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Search engine "%s": connection error: %s',
                $engine,
                $e->getMessage()
            ));
            return false;
        }
    }

    /**
     * Checks if given engine requires health monitoring
     *
     * @param string $engine Search engine code
     * @return bool
     */
    private function isSupportedEngine(string $engine): bool
    {
        return in_array($engine, self::SUPPORTED_ENGINES, true);
    }
}
