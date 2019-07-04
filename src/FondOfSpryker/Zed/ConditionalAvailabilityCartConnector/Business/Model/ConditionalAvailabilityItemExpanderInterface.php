<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Business\Model;

use Generated\Shared\Transfer\CartChangeTransfer;

interface ConditionalAvailabilityItemExpanderInterface
{
    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer;
}
