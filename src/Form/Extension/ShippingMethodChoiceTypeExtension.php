<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/magentix/pickup-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusPickupPlugin\Form\Extension;

use Magentix\SyliusPickupPlugin\Shipping\Calculator\CalculatorInterface as PickupCalculatorInterface;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodChoiceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShippingMethodChoiceTypeExtension extends AbstractTypeExtension
{

    /**
     * @var ServiceRegistryInterface $calculatorRegistry
     */
    private $calculatorRegistry;

    /**
     * @param ServiceRegistryInterface $calculatorRegistry
     */
    public function __construct(
        ServiceRegistryInterface $calculatorRegistry
    ) {
        $this->calculatorRegistry = $calculatorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault(
            'choice_attr',
            function ($choiceValue, $key, $value) {
                /** @var ShippingMethod $choiceValue */
                $calculatorName = $choiceValue->getCalculator();

                /** @var PickupCalculatorInterface $calculator */
                $calculator = $this->calculatorRegistry->get($calculatorName);

                $attr = [];
                if ($calculator instanceof PickupCalculatorInterface) {
                    $attr['class'] = 'pickup';
                }

                return $attr;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ShippingMethodChoiceType::class;
    }
}
