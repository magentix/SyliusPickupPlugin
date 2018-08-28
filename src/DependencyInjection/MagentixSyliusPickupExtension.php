<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/magentix/pickup-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusPickupPlugin\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class MagentixSyliusPickupExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
    }
}
