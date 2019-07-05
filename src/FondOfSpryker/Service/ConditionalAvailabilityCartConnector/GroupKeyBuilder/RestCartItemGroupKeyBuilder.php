<?php

namespace FondOfSpryker\Service\ConditionalAvailabilityCartConnector\GroupKeyBuilder;

use Generated\Shared\Transfer\RestCartItemTransfer;

class RestCartItemGroupKeyBuilder implements RestCartItemGroupKeyBuilderInterface
{
    /**
     * @var \FondOfSpryker\Service\ConditionalAvailabilityCartConnector\GroupKeyBuilder\GroupKeyBuilderInterface
     */
    protected $groupKeyBuilder;

    /**
     * @param \FondOfSpryker\Service\ConditionalAvailabilityCartConnector\GroupKeyBuilder\GroupKeyBuilderInterface $groupKeyBuilder
     */
    public function __construct(GroupKeyBuilderInterface $groupKeyBuilder)
    {
        $this->groupKeyBuilder = $groupKeyBuilder;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartItemTransfer $restCartItemTransfer
     *
     * @return string
     */
    public function build(RestCartItemTransfer $restCartItemTransfer): string
    {
        $deliveryDate = $restCartItemTransfer->getDeliveryTime();
        $sku = $restCartItemTransfer->getSku();

        if ($deliveryDate === null) {
            return $sku;
        }

        return $this->groupKeyBuilder->build($sku, $deliveryDate);
    }
}
