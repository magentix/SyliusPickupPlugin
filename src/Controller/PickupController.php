<?php

declare(strict_types=1);

namespace MagentixPickupPlugin\Controller;

use MagentixPickupPlugin\Shipping\Calculator\CalculatorInterface as PickupCalculatorInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

final class PickupController extends Controller
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
     * Display Pickup List
     *
     * @param Request $request
     * @param string $method
     * @param string|null $postcode
     * @param string|null $countryCode
     *
     * @return Response|string
     */
    public function listAction(Request $request, ?string $method, ?string $postcode, ?string $countryCode): Response
    {
        $calculator = $this->getCalculator($method);

        $pickupCurrentId = null;
        $pickupTemplate  = $this->getDefaultTemplate();
        $pickupList      = [];
        $pickupPostcode   = '';

        /** @var PickupCalculatorInterface $calculator */
        if ($calculator instanceof PickupCalculatorInterface) {
            if (!empty($calculator->getPickupTemplate())) {
                $pickupTemplate = $calculator->getPickupTemplate();
            }

            $cart = $this->getCurrentCart();
            if (null !== $cart->getId()) {
                $cart    = $this->getOrderRepository()->findCartForSummary($cart->getId());
                $address = $cart->getShippingAddress();

                $shipment = $cart->getShipments()->current();
                $pickupCurrentId = $shipment->getPickupId();

                if ($postcode) {
                    $address->setPostcode($postcode);
                }
                if ($countryCode) {
                    $address->setCountryCode($countryCode);
                }

                $pickupPostcode = $address->getPostcode();

                $configuration = [];
                $shippingMethod = $this->getMethod($method);
                if ($shippingMethod) {
                    $configuration = $shippingMethod->getConfiguration();
                }

                $pickupList = $calculator->getPickupList($address, $cart, $configuration);
            }
        }

        $method = [
            'pickup_list'  => $pickupList,
            'pickup_current_id' => $pickupCurrentId,
            'pickup_postcode' => $pickupPostcode,
            'index' => $request->get('index', 0),
            'method' => $method,
        ];

        return $this->render($pickupTemplate, ['method' => $method]);
    }

    /**
     * Retrieve Shipping Method Calculator
     *
     * @param string $shippingMethod
     * @return CalculatorInterface|bool
     */
    protected function getCalculator(string $shippingMethod): CalculatorInterface
    {
        $method = $this->getMethod($shippingMethod);

        if ($method === null) {
            return false;
        }

        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculatorRegistry->get($method->getCalculator());

        return $calculator;
    }

    /**
     * Retrieve Shipping Method
     *
     * @param string $shippingMethod
     * @return ShippingMethod|bool
     */
    protected function getMethod(?string $shippingMethod): ShippingMethod
    {
        /** @var ShippingMethod|null $method */
        $method = $this->getShippingMethodRepository()->findOneBy(['code' => $shippingMethod]);

        if ($method === null) {
            return false;
        }

        return $method;
    }

    /**
     * Retrieve Current Cart
     *
     * @return OrderInterface
     */
    protected function getCurrentCart(): OrderInterface
    {
        return $this->getContext()->getCart();
    }

    /**
     * Retrieve Cart Context
     *
     * @return CartContextInterface
     */
    protected function getContext(): CartContextInterface
    {
        /** @var CartContextInterface $context */
        $context = $this->get('sylius.context.cart');

        return $context;
    }

    /**
     * Retrieve Order Repository
     *
     * @return OrderRepositoryInterface
     */
    protected function getOrderRepository(): OrderRepositoryInterface
    {
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');

        return $orderRepository;
    }

    /**
     * Retrieve Shipping Mzthod Repository
     *
     * @return ShippingMethodRepositoryInterface
     */
    protected function getShippingMethodRepository(): ShippingMethodRepositoryInterface
    {
        /** @var ShippingMethodRepositoryInterface $shippingMethodRepository */
        $shippingMethodRepository = $this->get('sylius.repository.shipping_method');

        return $shippingMethodRepository;
    }

    /**
     * Retrieve default template for pickup list
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return '@MagentixPickupPlugin/checkout/SelectShipping/pickup/list.html.twig';
    }
}
