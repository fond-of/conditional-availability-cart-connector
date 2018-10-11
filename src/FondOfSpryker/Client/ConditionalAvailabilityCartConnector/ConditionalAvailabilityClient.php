<?php

namespace FondOfSpryker\Client\ConditionalAvailabilityCartConnector;

use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \FondOfSpryker\Client\ConditionalAvailabilityCartConnector\ConditionalAvailabilityCartConnectorFactory getFactory()
 */
class ConditionalAvailabilityClient extends AbstractClient implements ConditionalAvailabilityClientInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $searchString
     * @param array $requestParameters
     *
     * @return array
     */
    public function availabilitySearch($searchString, array $requestParameters = [])
    {
        $searchQuery = $this
            ->getFactory()
            ->createConditionalAvailabilityQuery($searchString);

        $searchQuery = $this
            ->getFactory()
            ->getConditionalAvailabilityClient();

        /*
        $resultFormatters = $this
            ->getFactory()
            ->getCatalogSearchResultFormatters();*/

        return $this
            ->getFactory()
            ->getConditionalAvailabilityClient()
            ->search($searchQuery, [], $requestParameters);
    }
}
