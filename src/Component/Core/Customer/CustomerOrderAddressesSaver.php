<?php
/**
 * @author    Paweł Jędrzejewski
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/magentix/pickup-plugin
 */
declare(strict_types=1);

namespace MagentixPickupPlugin\Component\Core\Customer;

use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Customer\CustomerAddressAdderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CustomerOrderAddressesSaver implements OrderAddressesSaverInterface
{
    /**
     * @var CustomerAddressAdderInterface
     */
    private $addressAdder;

    /**
     * @param CustomerAddressAdderInterface $addressAdder
     */
    public function __construct(CustomerAddressAdderInterface $addressAdder)
    {
        $this->addressAdder = $addressAdder;
    }

    /**
     * @param OrderInterface $order
     */
    public function saveAddresses(OrderInterface $order): void
    {
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();
        if (null === $customer->getUser()) {
            return;
        }

        $this->addAddress($customer, $order->getBillingAddress());
        if (!$order->getShipments()->current()->getPickupId()) {
            $this->addAddress($customer, $order->getShippingAddress());
        }
    }

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface|null $address
     */
    private function addAddress(CustomerInterface $customer, ?AddressInterface $address): void
    {
        if (null !== $address) {
            $this->addressAdder->add($customer, clone $address);
        }
    }
}
