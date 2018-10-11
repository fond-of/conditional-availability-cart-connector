<?php

namespace FondOfSpryker\Client\ConditionalAvailabilityCartConnector;

use Spryker\Client\Catalog\Listing\CatalogViewModePersistence;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\Search\Dependency\Plugin\SearchStringSetterInterface;

class ConditionalAvailabilityCartConnectorFactory extends AbstractFactory
{
    /**
     * @param string $searchString
     *
     * @return \Spryker\Client\Search\Dependency\Plugin\QueryInterface
     */
    public function createConditionalAvailabilityQuery($searchString)
    {
        $searchQuery = $this->getConditionalAvailabilityQueryPlugin();

        if ($searchQuery instanceof SearchStringSetterInterface) {
            $searchQuery->setSearchString($searchString);
        }

        return $searchQuery;
    }

    /**
     * @return \Spryker\Client\Search\SearchClientInterface
     */
    public function getConditionalAvailabilityClient()
    {
        return $this->getProvidedDependency(ConditionalAvailabilityCartConnectorDependencyProvider::CLIENT_CONDITIONAL_AVAILABILITY);
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\QueryInterface
     */
    public function getConditionalAvailabilityQueryPlugin()
    {
        return $this->getProvidedDependency(ConditionalAvailabilityCartConnectorDependencyProvider::CONDITIONAL_AVAILABILITY_QUERY_PLUGIN);
    }

}
