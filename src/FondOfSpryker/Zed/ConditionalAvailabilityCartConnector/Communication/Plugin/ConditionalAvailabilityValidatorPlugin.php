<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Communication\Plugin;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\Cart\Dependency\ItemExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Business\ProductBundleFacadeInterface getFacade()
 * @method \FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Communication\ProductBundleCommunicationFactory getFactory()
 */
class ConditionalAvailabilityValidatorPlugin extends AbstractPlugin implements ItemExpanderPluginInterface
{
    public const GROUP_KEY_DELIMITER = '-';

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer)
    {
        foreach ($cartChangeTransfer->getItems() as $cartItem) {
            $this->validateAvailability($cartItem);
        }

        return $cartChangeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $cartItem
     *
     * @return string
     */
    protected function validateAvailability(ItemTransfer $cartItem)
    {
        $deliveryTime = $cartItem->getDeliveryTime();

        if (empty($deliveryTime)) {
            $sku = $cartItem->getSku();
            $qty = $cartItem->getQuantity();

            \dump($sku);
            \dump($qty);
        }
        exit;
    }
}
