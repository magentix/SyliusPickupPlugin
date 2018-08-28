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
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShipmentType;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface;

final class CheckoutShipmentTypeExtension extends AbstractTypeExtension
{

    /**
     * @var ServiceRegistryInterface $calculatorRegistry
     */
    private $calculatorRegistry;

    /**
     * @var ShippingMethodRepositoryInterface $shippingMethodRepository
     */
    private $shippingMethodRepository;

    /**
     * @var TranslatorInterface $translator
     */
    private $translator;

    /**
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ServiceRegistryInterface $calculatorRegistry,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        TranslatorInterface $translator
    ) {
        $this->calculatorRegistry = $calculatorRegistry;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();

                $form->add('pickup_id', TextType::class, [
                    'required' => false,
                    'label' => 'sylius.form.checkout.shipping_method.pickup_id',
                ]);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                $method = $this->shippingMethodRepository->findOneBy(['code' => $data['method']]);
                if ($method) {
                    $calculator = $this->calculatorRegistry->get($method->getCalculator());

                    if ($calculator instanceof PickupCalculatorInterface) {
                        if (!isset($data['pickup_id'])) {
                            $event->getForm()->addError(
                                new FormError($this->translator->trans('pickup.form.shipment.error.pickup_id'))
                            );
                        }
                    }
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ShipmentType::class;
    }
}
