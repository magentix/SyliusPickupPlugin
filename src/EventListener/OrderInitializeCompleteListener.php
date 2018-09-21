<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/magentix/pickup-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusPickupPlugin\EventListener;

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
            $shipping = clone $order->getShippingAddress();
            $shipping->setCompany($pickup['company']);
            $shipping->setStreet($pickup['street_1']);
            $shipping->setCity($pickup['city']);
            $shipping->setPostcode($pickup['postcode']);
            $shipping->setCountryCode($pickup['country']);

            $order->setShippingAddress($shipping);
        }
    }
}
