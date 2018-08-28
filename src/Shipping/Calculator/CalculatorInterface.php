<?php

declare(strict_types=1);

namespace Magentix\SyliusPickupPlugin\Shipping\Calculator;

use Sylius\Component\Shipping\Calculator\CalculatorInterface as BaseCalculatorInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

interface CalculatorInterface extends BaseCalculatorInterface
{

    /**
     * Retrieve pickup list
     *
     * @param AddressInterface $address
     * @param OrderInterface $cart
     * @param ShippingMethodInterface $shippingMethod
     * @return array
     */
    public function getPickupList(
        AddressInterface $address,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod): array;

    /**
     * Retrieve pickup Address
     *
     * @param string $pickupId
     * @param ShippingMethodInterface $shippingMethod
     * @return array
     */
    public function getPickupAddress(string $pickupId, ShippingMethodInterface $shippingMethod): array;

    /**
     * Retrieve pickup template
     *
     * @return string
     */
    public function getPickupTemplate(): string;
}
