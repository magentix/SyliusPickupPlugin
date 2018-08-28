<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/magentix/pickup-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusPickupPlugin\EventListener;

use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Factory\AddressFactory;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderPreCompleteListener extends PickupListener
{

    /**
     * @var AddressFactory $addressFactory
     */
    private $addressFactory;

    /**
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        ServiceRegistryInterface $calculatorRegistry,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        AddressFactory $addressFactory
    ) {
        parent::__construct($calculatorRegistry, $shippingMethodRepository, $shipmentRepository);

        $this->addressFactory = $addressFactory;
    }

    /**
     * @param GenericEvent $event
     */
    public function addShippingAddress(GenericEvent $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        $pickup = $this->getPickupAddress($order);

        if (!empty($pickup)) {
            $shipping = $order->getShippingAddress();

            $address = $this->addressFactory->createNew();
            $address->setFirstName($shipping->getFirstName());
            $address->setLastName($shipping->getLastName());
            $address->setPhoneNumber($shipping->getPhoneNumber());
            $address->setCompany($pickup['company']);
            $address->setCity($pickup['city']);
            $address->setStreet($pickup['street_1']);
            $address->setPostcode($pickup['postcode']);
            $address->setCountryCode($pickup['country']);

            $order->setShippingAddress($address);
        }
    }
}
