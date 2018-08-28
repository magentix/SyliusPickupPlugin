<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/magentix/pickup-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusPickupPlugin\Entity;

use Sylius\Component\Core\Model\Shipment as BaseShipment;

class Shipment extends BaseShipment
{
    /**
     * @var string $pickup_id
     */
    private $pickup_id;

    /**
     * @return string|null
     */
    public function getPickupId(): ?string
    {
        return $this->pickup_id;
    }

    /**
     * @param string $pickupId
     */
    public function setPickupId(?string $pickupId): void
    {
        $this->pickup_id = $pickupId;
    }
}
