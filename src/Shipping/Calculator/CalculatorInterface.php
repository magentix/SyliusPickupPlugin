<?php

declare(strict_types=1);

namespace MagentixPickupPlugin\Shipping\Calculator;

use Sylius\Component\Shipping\Calculator\CalculatorInterface as BaseCalculatorInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface CalculatorInterface extends BaseCalculatorInterface
{

    /**
     * Retrieve pickup list
     *
     * @param AddressInterface $address
     * @param OrderInterface $cart
     * @param array $configuration
     * @return array
     */
    public function getPickupList(AddressInterface $address, OrderInterface $cart, array $configuration): array;

    /**
     * Retrieve pickup Address
     *
     * @param string $pickupId
     * @param array $configuration
     * @return array
     */
    public function getPickupAddress(string $pickupId, array $configuration): array;

    /**
     * Retrieve pickup template
     *
     * @return string
     */
    public function getPickupTemplate(): string;
}
