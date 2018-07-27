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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

final class PickupController extends Controller
{

    /**
     * @var ServiceRegistryInterface $calculatorRegistry
     */
    private $calculatorRegistry;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(
        ServiceRegistryInterface $calculatorRegistry,
        RepositoryInterface $countryRepository
    ) {
        $this->calculatorRegistry = $calculatorRegistry;
        $this->countryRepository  = $countryRepository;
    }

    /**
     * Display Pickup List
     *
     * @param Request $request
     * @param string $method
     * @return Response|string
     */
    public function listAction(Request $request, ?string $method): Response
    {
        $calculator = $this->getCalculator($method);

        $params = $request->request->all();

        $pickupTemplate  = $this->getDefaultTemplate();
        $pickupCurrentId = null;
        $pickupList      = [];
        $pickupAddress   = null;

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

                foreach ($params as $field => $value) {
                    $setter = 'set' . preg_replace('/_/', '', ucwords($field, '_'));
                    if (method_exists($address, $setter)) {
                        $address->$setter($value);
                    }
                }

                $pickupAddress = $address;

                $configuration = [];
                $shippingMethod = $this->getMethod($method);
                if ($shippingMethod) {
                    $configuration = $shippingMethod->getConfiguration();
                }

                $pickupList = $calculator->getPickupList($address, $cart, $configuration);
            }
        }

        $pickup = [
            'pickup_current_id' => $pickupCurrentId,
            'pickup_list'       => $pickupList,
            'pickup_address'    => $pickupAddress,
            'countries'         => $this->getAvailableCountries(),
            'index'             => $request->get('index', 0),
            'method'            => $method,
        ];

        return $this->render($pickupTemplate, ['method' => $pickup]);
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
     * @return array|CountryInterface[]
     */
    private function getAvailableCountries(): array
    {
        $countries = Intl::getRegionBundle()->getCountryNames();

        /** @var CountryInterface[] $definedCountries */
        $definedCountries = $this->countryRepository->findAll();

        $availableCountries = [];

        foreach ($definedCountries as $country) {
            $availableCountries[$country->getCode()] = $countries[$country->getCode()];
        }

        return $availableCountries;
    }

    /**
     * Retrieve default template for pickup list
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return '@MagentixPickupPlugin/checkout/SelectShipping/pickup/list.html.twig';
    }
}
