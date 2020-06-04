<?php

namespace FondOfSpryker\Glue\ConditionalAvailabilityCartConnector\Plugin\CompanyUserCartsRestApi;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Plugin\RestCartItemExpanderPluginInterface;
use Generated\Shared\Transfer\RestCartItemTransfer;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \FondOfSpryker\Glue\ConditionalAvailabilityCartConnector\ConditionalAvailabilityCartConnectorFactory getFactory()
 */
class ConditionalAvailabilityCartConnectorGroupKeyRestCartItemExpanderPlugin extends AbstractPlugin implements RestCartItemExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\RestCartItemTransfer $restCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\RestCartItemTransfer
     */
    public function expand(RestCartItemTransfer $restCartItemTransfer): RestCartItemTransfer
    {
        $groupKey = $this->getFactory()->getService()->buildRestCartItemGroupKey($restCartItemTransfer);

        $restCartItemTransfer->setGroupKey($groupKey);

        return $restCartItemTransfer;
    }
}
