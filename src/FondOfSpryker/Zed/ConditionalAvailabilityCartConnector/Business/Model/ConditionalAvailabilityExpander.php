<?php

namespace FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Business\Model;

use DateTime;
use FondOfSpryker\Shared\ConditionalAvailability\ConditionalAvailabilityConstants;
use FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Dependency\Client\ConditionalAvailabilityCartConnectorToConditionalAvailabilityClientInterface;
use FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Dependency\Service\ConditionalAvailabilityCartConnectorToConditionalAvailabilityServiceInterface;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use function array_unique;

class ConditionalAvailabilityExpander implements ConditionalAvailabilityExpanderInterface
{
    protected const SEARCH_KEY = 'periods';
    protected const DELIVERY_DATE_FORMAT = 'Y-m-d';

    protected const MESSAGE_TYPE_ERROR = 'error';

    protected const MESSAGE_NOT_AVAILABLE_FOR_GIVEN_DELIVERY_DATE = 'conditional_availability_cart_connector.not_available_for_given_delivery_date';
    protected const MESSAGE_NOT_AVAILABLE_FOR_EARLIEST_DELIVERY_DATE = 'conditional_availability_cart_connector.not_available_for_earliest_delivery_date';
    protected const MESSAGE_NOT_AVAILABLE_FOR_GIVEN_QTY = 'conditional_availability_cart_connector.not_available_for_given_qty';

    /**
     * @var \FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Dependency\Client\ConditionalAvailabilityCartConnectorToConditionalAvailabilityClientInterface
     */
    protected $conditionalAvailabilityClient;

    /**
     * @var \FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Dependency\Service\ConditionalAvailabilityCartConnectorToConditionalAvailabilityServiceInterface
     */
    protected $conditionalAvailabilityService;

    /**
     * @var string[]
     */
    private $deliveryDates = [];

    /**
     * @var string[]
     */
    private $concreteDeliveryDates = [];

    /**
     * @var array
     */
    protected $defaultRequestParameters = [];

    /**
     * @param \FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Dependency\Client\ConditionalAvailabilityCartConnectorToConditionalAvailabilityClientInterface $conditionalAvailabilityClient
     * @param \FondOfSpryker\Zed\ConditionalAvailabilityCartConnector\Dependency\Service\ConditionalAvailabilityCartConnectorToConditionalAvailabilityServiceInterface $conditionalAvailabilityService
     */
    public function __construct(
        ConditionalAvailabilityCartConnectorToConditionalAvailabilityClientInterface $conditionalAvailabilityClient,
        ConditionalAvailabilityCartConnectorToConditionalAvailabilityServiceInterface $conditionalAvailabilityService
    ) {
        $this->conditionalAvailabilityClient = $conditionalAvailabilityClient;
        $this->conditionalAvailabilityService = $conditionalAvailabilityService;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expand(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $this->initDefaultRequestParameters($quoteTransfer);

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $this->expandItem($itemTransfer);
        }

        $quoteTransfer->setDeliveryDates($this->createUniqueDates($this->deliveryDates))
            ->setConcreteDeliveryDates($this->createUniqueDates($this->concreteDeliveryDates));

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function expandItem(ItemTransfer $itemTransfer): ItemTransfer
    {
        if ($itemTransfer->getDeliveryDate() === ConditionalAvailabilityConstants::KEY_EARLIEST_DATE) {
            return $this->expandItemWithEarliestDeliveryDate($itemTransfer);
        }

        return $this->expandItemWithConcreteDeliveryDate($itemTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @throws
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function expandItemWithEarliestDeliveryDate(ItemTransfer $itemTransfer): ItemTransfer
    {
        $earliestDeliveryDate = $this->conditionalAvailabilityService->generateEarliestDeliveryDate();

        $requestParameters = array_merge($this->defaultRequestParameters, [
            ConditionalAvailabilityConstants::PARAMETER_START_AT => $earliestDeliveryDate,
        ]);

        $result = $this->conditionalAvailabilityClient->conditionalAvailabilitySkuSearch(
            $itemTransfer->getSku(),
            $requestParameters
        );

        if (!array_key_exists(static::SEARCH_KEY, $result) || count($result[static::SEARCH_KEY]) === 0) {
            return $itemTransfer->addValidationMessage($this->createNotAvailableForEarliestDeliveryDateMessage());
        }

        foreach ($result[static::SEARCH_KEY] as $period) {
            $periodQuantity = (int)$period['qty'];
            $periodStartAt = $period['startAt'];

            if ($periodQuantity < $itemTransfer->getQuantity()) {
                $itemTransfer->addValidationMessage($this->createNotAvailableForGivenQytMessage());
            }

            $itemTransfer->setDeliveryDate(ConditionalAvailabilityConstants::KEY_EARLIEST_DATE);
            $startAtString = (new DateTime($periodStartAt))->format(static::DELIVERY_DATE_FORMAT);
            $itemTransfer->setConcreteDeliveryDate($startAtString);

            $this->deliveryDates[] = ConditionalAvailabilityConstants::KEY_EARLIEST_DATE;
            $this->concreteDeliveryDates[] = $startAtString;

            break;
        }

        return $itemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @throws
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function expandItemWithConcreteDeliveryDate(ItemTransfer $itemTransfer): ItemTransfer
    {
        $concreteDeliveryDate = $itemTransfer->getDeliveryDate();
        $startAndEndAt = new DateTime($concreteDeliveryDate);

        $requestParameters = array_merge($this->defaultRequestParameters, [
            ConditionalAvailabilityConstants::PARAMETER_START_AT => $startAndEndAt,
            ConditionalAvailabilityConstants::PARAMETER_END_AT => $startAndEndAt,
        ]);

        $result = $this->conditionalAvailabilityClient->conditionalAvailabilitySkuSearch(
            $itemTransfer->getSku(),
            $requestParameters
        );

        if (!array_key_exists(static::SEARCH_KEY, $result) || count($result[static::SEARCH_KEY]) === 0) {
            return $itemTransfer->addValidationMessage($this->createNotAvailableForGivenDeliveryDateMessage());
        }

        foreach ($result[static::SEARCH_KEY] as $period) {
            $periodQuantity = (int)$period['qty'];

            if ($periodQuantity < $itemTransfer->getQuantity()) {
                $itemTransfer->addValidationMessage($this->createNotAvailableForGivenQytMessage());
            }

            $itemTransfer->setDeliveryDate($concreteDeliveryDate)
                ->setConcreteDeliveryDate($concreteDeliveryDate);

            $this->deliveryDates[] = $concreteDeliveryDate;
            $this->concreteDeliveryDates[] = $concreteDeliveryDate;
        }

        return $itemTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createNotAvailableForGivenDeliveryDateMessage(): MessageTransfer
    {
        $messageTransfer = new MessageTransfer();

        $messageTransfer->setType(static::MESSAGE_TYPE_ERROR)
            ->setValue(static::MESSAGE_NOT_AVAILABLE_FOR_GIVEN_DELIVERY_DATE);

        return $messageTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createNotAvailableForEarliestDeliveryDateMessage(): MessageTransfer
    {
        $messageTransfer = new MessageTransfer();

        $messageTransfer->setType(static::MESSAGE_TYPE_ERROR)
            ->setValue(static::MESSAGE_NOT_AVAILABLE_FOR_EARLIEST_DELIVERY_DATE);

        return $messageTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createNotAvailableForGivenQytMessage(): MessageTransfer
    {
        $messageTransfer = new MessageTransfer();

        $messageTransfer->setType(static::MESSAGE_TYPE_ERROR)
            ->setValue(static::MESSAGE_NOT_AVAILABLE_FOR_GIVEN_QTY);

        return $messageTransfer;
    }

    /**
     * @param array $dates
     *
     * @return array
     */
    protected function createUniqueDates(array $dates): array
    {
        return array_values(array_unique($dates));
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return $this
     */
    protected function initDefaultRequestParameters(QuoteTransfer $quoteTransfer): ConditionalAvailabilityExpanderInterface
    {
        $this->defaultRequestParameters = [
            ConditionalAvailabilityConstants::PARAMETER_WAREHOUSE => ConditionalAvailabilityConstants::DEFAULT_WAREHOUSE,
            ConditionalAvailabilityConstants::PARAMETER_QUOTE_TRANSFER => $quoteTransfer,
        ];

        return $this;
    }
}
