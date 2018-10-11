<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\ProductPageSearch\Dependency\Facade\ProductPageSearchToPriceFacadeInterface;
use Spryker\Zed\ProductPageSearch\Dependency\Service\ProductPageSearchToPriceProductServiceInterface;
use Spryker\Zed\ProductPageSearch\ProductPageSearchDependencyProvider;

/**
 * @method \Spryker\Zed\ProductPageSearch\Persistence\ProductPageSearchQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductPageSearch\ProductPageSearchConfig getConfig()
 */
class ConditionalAvailabilityCartConnectorCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \FondOfSpryker\Zed\ProductPageSearch\Dependency\Facade\ProductPageSearchToProductSearchInterface
     */
    public function getProductSearchFacade()
    {
        return $this->getProvidedDependency(ProductPageSearchDependencyProvider::FACADE_PRODUCT_SEARCH);
    }
}
