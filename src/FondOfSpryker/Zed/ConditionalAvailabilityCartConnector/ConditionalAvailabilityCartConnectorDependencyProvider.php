<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCartConnector;

class ConditionalAvailabilityCartConnectorDependencyProvider
{
    const CLIENT_CONDITIONAL_AVAILABILITY = 'conditional availability client';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addConditionalAvailabilityClient(Container $container)
    {
        $container[static::CLIENT_CONDITIONAL_AVAILABILITY] = function (Container $container) {
            return $container->getLocator()->conditionAvailability()->client();
        };

        return $container;
    }

    /**
     * @return \Spryker\Client\Search\Dependency\Plugin\QueryInterface
     */
    protected function createConditionalAvailabilityQueryPlugin()
    {
        return new CatalogSearchQueryPlugin();
    }

}
