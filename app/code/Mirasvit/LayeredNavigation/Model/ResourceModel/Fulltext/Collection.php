<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-navigation
 * @version   1.1.2
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Model\ResourceModel\Fulltext;

use Magento\Catalog\Model\Indexer\Product\Flat\State;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Helper;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Module\Manager;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Magento\Framework\Search\Response\QueryResponse;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\SearchEngine;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\LayeredNavigation\Model\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Model\Layer\Filter\Attribute;
use Mirasvit\LayeredNavigation\Model\Request\Builder;
use Mirasvit\LayeredNavigation\Service\Filter\ApplyAdditionalFilters;
use Psr\Log\LoggerInterface;

/**
 * Fulltext Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    use ConfigTrait;

    /** @var  \Magento\Framework\Search\ResponseInterface */
    private $queryResponse;

    /**
     * Catalog search data
     * @var \Magento\Search\Model\QueryFactory
     */
    private $queryFactory = null;

    /**
     * \Mirasvit\LayeredNavigation\Model\Request\Builder
     */
    private $requestBuilder;

    /**
     * @var \Magento\Search\Model\SearchEngine
     */
    private $searchEngine;

    /**
     * @var string
     */
    private $queryText;

    /**
     * @var array|null
     */
    private $order = null;

    /**
     * @var string
     */
    private $searchRequestName;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory
     */
    private $temporaryStorageFactory;

    /**
     * \Mirasvit\LayeredNavigation\Model\Request\Builder
     */
    private $cloneRequestBuilder;

    /**
     * @var ApplyAdditionalFilters
     */
    private $applyAdditionalFilters;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @param ApplyAdditionalFilters   $applyAdditionalFilters
     * @param EntityFactory            $entityFactory
     * @param LoggerInterface          $logger
     * @param FetchStrategyInterface   $fetchStrategy
     * @param ManagerInterface         $eventManager
     * @param Config                   $eavConfig
     * @param ResourceConnection       $resource
     * @param EavEntityFactory         $eavEntityFactory
     * @param Helper                   $resourceHelper
     * @param UniversalFactory         $universalFactory
     * @param StoreManagerInterface    $storeManager
     * @param Manager                  $moduleManager
     * @param State                    $catalogProductFlatState
     * @param ScopeConfigInterface     $scopeConfig
     * @param OptionFactory            $productOptionFactory
     * @param Url                      $catalogUrl
     * @param TimezoneInterface        $localeDate
     * @param Session                  $customerSession
     * @param DateTime                 $dateTime
     * @param GroupManagementInterface $groupManagement
     * @param QueryFactory             $queryFactory
     * @param Builder                  $requestBuilder
     * @param SearchEngine             $searchEngine
     * @param TemporaryStorageFactory  $temporaryStorageFactory
     * @param null                     $connection
     * @param string                   $searchRequestName
     */
    public function __construct(
        ApplyAdditionalFilters $applyAdditionalFilters,
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $eavConfig,
        ResourceConnection $resource,
        EavEntityFactory $eavEntityFactory,
        Helper $resourceHelper,
        UniversalFactory $universalFactory,
        StoreManagerInterface $storeManager,
        Manager $moduleManager,
        State $catalogProductFlatState,
        ScopeConfigInterface $scopeConfig,
        OptionFactory $productOptionFactory,
        Url $catalogUrl,
        TimezoneInterface $localeDate,
        Session $customerSession,
        DateTime $dateTime,
        GroupManagementInterface $groupManagement,
        QueryFactory $queryFactory,
        Builder $requestBuilder,
        SearchEngine $searchEngine,
        TemporaryStorageFactory $temporaryStorageFactory,
        $connection = null,
        $searchRequestName = 'catalog_view_container'
    ) {
        $this->queryFactory      = $queryFactory;
        $this->searchRequestName = $searchRequestName;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection
        );

        $this->requestBuilder          = $requestBuilder;
        $this->searchEngine            = $searchEngine;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->applyAdditionalFilters  = $applyAdditionalFilters;
    }

    /**
     * Search query filter
     *
     * @param string $query
     *
     * @return $this
     */
    public function addSearchFilter($query)
    {
        $this->queryText = trim($this->queryText . ' ' . $query);

        return $this;
    }

    /**
     * @param \Mirasvit\LayeredNavigation\Model\Request\Builder $builder
     */
    public function setRequestData($builder)
    {
        $this->requestBuilder     = $builder;
        $this->queryResponse      = null;
        $this->_isFiltersRendered = false;
    }

    /**
     * @return \Mirasvit\LayeredNavigation\Model\Request\Builder
     */
    public function getCloneRequestBuilder()
    {
        if (!$this->cloneRequestBuilder) {
            $this->createdRequestBuilder();
        }

        return $this->cloneRequestBuilder;
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     *
     * @return $this
     */
    public function setOrder($attribute, $dir = Select::SQL_DESC)
    {
        $this->order = ['field' => $attribute, 'dir' => $dir];
        if ($attribute != 'relevance') {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    /**
     * Stub method for compatibility with other search engines
     * @return $this
     */
    public function setGeneralDefaultQuery()
    {
        return $this;
    }

    /**
     * @param string             $field
     * @param QueryResponse|null $response
     *
     * @return array
     * @throws StateException
     */
    public function getFacetedData($field, QueryResponse $response = null)
    {
        $this->_renderFilters();
        $response     = $response ? $response : $this->queryResponse;
        $aggregations = $response->getAggregations();
        $bucket       = $aggregations->getBucket($field . '_bucket');
        if (!$bucket) {
            return [];
        }

        $result = [];

        $skipZeroRange = false;
        foreach ($bucket->getValues() as $value) {
            $metrics = $value->getMetrics();

            if ($metrics['value'] == 'sliderdataprice' && isset($metrics['min']) && $metrics['min'] == 0) {
                $skipZeroRange = true;
            }

            if ($skipZeroRange && substr($metrics['value'], -2) === '_0') {
                $skipZeroRange = false;
                continue;
            }

            $result[$metrics['value']] = $metrics;
        }

        return $result;
    }

    /**
     * Specify category filter for product collection
     *
     * @param \Magento\Catalog\Model\Category $category
     *
     * @return $this
     */
    public function addCategoryFilter(\Magento\Catalog\Model\Category $category)
    {
        $this->addFieldToFilter('category_ids', $category->getId());

        return parent::addCategoryFilter($category);
    }

    /**
     * @param array $categoryIds
     *
     * @return $this
     */
    public function addCategoryMultiFilter($categoryIds)
    {
        $this->addFieldToFilter('category_ids', ['in' => $categoryIds]);

        return $this;
    }

    /**
     * Apply attribute filter to facet collection
     *
     * @param string            $field
     * @param null|array|string $condition
     *
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($this->queryResponse !== null) {
            throw new \RuntimeException('Illegal state');
        }
        if (!is_array($condition)
            || (!in_array(key($condition), ['from', 'to'], true)
                && $field != 'visibility')) {
            $this->requestBuilder->bind($field, $condition);
        } else {
            if (!empty($condition['from'])) {
                $this->requestBuilder->bind("{$field}.from", $condition['from']);
            }
            if (!empty($condition['to'])) {
                $this->requestBuilder->bind("{$field}.to", $condition['to']);
            }
        }

        return $this;
    }

    /**
     * Set product visibility filter for enabled products
     *
     * @param array $visibility
     *
     * @return $this
     */
    public function setVisibility($visibility)
    {
        $this->addFieldToFilter('visibility', $visibility);

        return parent::setVisibility($visibility);
    }

    /**
     * Get collection size
     * @return int
     */
    public function getSize()
    {
        $sql                 = $this->getSelectCountSql();
        $this->_totalRecords = (int)$this->getConnection()->fetchOne($sql, $this->_bindParams);

        return intval($this->_totalRecords);
    }

    /**
     * Filter Product by Categories
     *
     * @param array $pricesFilter
     *
     * @return $this
     */
    public function addPricesFilter(array $pricesFilter)
    {
        foreach ($pricesFilter as $field => $condition) {
            foreach ($condition as $key => $value) {
                if (!$value['to']) {
                    unset($condition[$key]['to']);
                }
                if (!$value['from']) {
                    unset($condition[$key]['from']);
                }
            }
            $this->getSelect()->where($this->getConnection()->prepareSqlCondition($field, $condition));
        }

        return $this;
    }

    /**
     * Hook for operations before rendering filters
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->requestBuilder->bindDimension('scope', $this->getStoreId());

        if (!CompatibilityService::is20() && !CompatibilityService::is21()) {
            $productListVisibilityFilter = (int)Visibility::VISIBILITY_IN_CATALOG;
            if ($this->requestBuilder->hasPlaceholder('search_term')) {
                $productListVisibilityFilter = (int)Visibility::VISIBILITY_IN_SEARCH;
            }

            if (is_numeric($productListVisibilityFilter)) {
                $productListVisibility[] = $productListVisibilityFilter;
                $productListVisibility[] = (int)Visibility::VISIBILITY_BOTH;
                $this->requestBuilder->bind('visibility', $productListVisibility);
            }
        }

        if ($this->queryText) {
            $this->requestBuilder->bind('search_term', $this->queryText);
        } elseif ($this->searchRequestName === 'quick_search_container') {
            $temporaryStorage = $this->temporaryStorageFactory->create();
            $table            = $temporaryStorage->storeApiDocuments([]);

            $this->getSelect()->joinInner([
                'search_result' => $table->getName(),
            ], 'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID, []);

            $this->_totalRecords = 0;

            return parent::_renderFiltersBefore();
        }


        $priceRangeCalculation = $this->_scopeConfig->getValue(
            \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($priceRangeCalculation) {
            $this->requestBuilder->bind('price_dynamic_algorithm', $priceRangeCalculation);
        }

        $this->requestBuilder->setRequestName($this->searchRequestName);
        $this->cloneRequestBuilder = clone $this->requestBuilder;

        // apply some additional filters, that cannot be used with "addFieldToFilter" method
        $this->applyAdditionalFilters->apply($this->requestBuilder);

        $queryRequest        = $this->requestBuilder->create();
        $this->queryResponse = $this->searchEngine->search($queryRequest);

        // save response to attribute filter cache to avoid duplicate searches
        // fixes issue when "price navigation step calculation" set to improved algorithm
        $hash = $this->requestBuilder->hash($queryRequest);

        Attribute::$responseCache[$hash] = $this->queryResponse;

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table            = $temporaryStorage->storeApiDocuments($this->queryResponse->getIterator());

        $this->getSelect()->joinInner([
            'search_result' => $table->getName(),
        ], 'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID, []);

        $this->_totalRecords = $this->queryResponse->count();

        if ($this->order && 'relevance' === $this->order['field']) {
            if (!$this->order['dir']) {
                $this->order['dir'] = 'desc';
            }
            $this->getSelect()->order('search_result.' . TemporaryStorage::FIELD_SCORE . ' ' . $this->order['dir']);
        }

        return parent::_renderFiltersBefore();
    }

    /**
     * Add order by entity_id
     * @return $this
     */
    protected function _renderOrders()
    {
        if (!$this->_isOrdersRendered) {
            parent::_renderOrders();
            if (count($this->_orders)) { //fix for search engines)
                $filters = $this->_productLimitationFilters;
                if (isset($filters['category_id']) || isset($filters['visibility'])) {
                    $this->getSelect()->order("e.entity_id ASC");
                }
            }
        }

        return $this;
    }

    /**
     * @return void
     */
    private function createdRequestBuilder()
    {
        $this->cloneRequestBuilder = clone $this->requestBuilder;
        $this->cloneRequestBuilder->bindDimension('scope', $this->getStoreId());
        if ($this->queryText) {
            $this->cloneRequestBuilder->bind('search_term', $this->queryText);
        }

        $this->cloneRequestBuilder->setRequestName($this->searchRequestName);
    }
}
