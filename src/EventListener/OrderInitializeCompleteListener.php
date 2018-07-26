<?php

declare(strict_types=1);

namespace MagentixPickupPlugin\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\Order;

final class OrderInitializeCompleteListener extends PickupListener
{

    /**
     * Update Shipping Address on cart summary
     *
     * @param ResourceControllerEvent $event
     * @return void
     */
    public function updateShippingAddress(ResourceControllerEvent $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        $pickup = $this->getPickupAddress($order);

        if (!empty($pickup)) {
            $shipping = $order->getShippingAddress();
            $shipping->setCompany($pickup['company']);
            $shipping->setStreet($pickup['street_1']);
            $shipping->setCity($pickup['city']);
            $shipping->setPostcode($pickup['postcode']);
            $shipping->setCountryCode($pickup['country']);
        }
    }
}
