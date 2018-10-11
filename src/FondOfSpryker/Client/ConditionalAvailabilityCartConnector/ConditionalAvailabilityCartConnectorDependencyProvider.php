<?php

namespace FondOfSpryker\Client\ConditionalAvailabilityCartConnector;

use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class ConditionalAvailabilityCartConnectorDependencyProvider extends AbstractDependencyProvider
{
    const CLIENT_CONDITIONAL_AVAILABILITY = 'conditional availabilty client';
    const CONDITIONAL_AVAILABILITY_QUERY_PLUGIN = 'search query plugin';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container)
    {
        $container = parent::provideServiceLayerDependencies($container);

        $container = $this->addConditionalAvailabilityClient($container);
        $container = $this->addConditionalAvailabilityQueryPlugin($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addConditionalAvailabilityClient(Container $container)
    {
        $container[static::CLIENT_CONDITIONAL_AVAILABILITY] = function (Container $container) {
            return $container->getLocator()->search()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addConditionalAvailabilityQueryPlugin(Container $container)
    {
        $container[static::CONDITIONAL_AVAILABILITY_QUERY_PLUGIN] = function () {
            return $this->createConditionalAvailabilityQueryPlugin();
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
