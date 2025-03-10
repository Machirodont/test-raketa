<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Repository\ProductRepository;

readonly class CartView
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductsView $productsView,
    ) {
    }

    public function toArray(Cart $cart): array
    {
        $data = [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $cart->getCustomer()->getId(),
                'name' => implode(' ', [
                    $cart->getCustomer()->getLastName(),
                    $cart->getCustomer()->getFirstName(),
                    $cart->getCustomer()->getMiddleName(),
                ]),
                'email' => $cart->getCustomer()->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
        ];

        $productList = $this->productRepository->getByUuidList($cart->getProductsUuidList());

        $total = '0';
        $data['items'] = [];
        foreach ($cart->getItems() as $item) {
            $itemTotal = bcmul($item->getPrice(),(string)$item->getQuantity(),Cart::PRICE_DEFAULT_SCALE);
            $total = bcadd($total,$itemTotal,Cart::PRICE_DEFAULT_SCALE);
            $product = $productList->getProductByUuid($item->getProductUuid());

            $data['items'][] = [
                'uuid' => $item->getUuid(),
                'price' => $item->getPrice(),
                'total' => $itemTotal,
                'quantity' => $item->getQuantity(),
                'product' => $product ? $this->productsView->toArray($product) : null,
            ];
        }

        $data['total'] = $total;

        return $data;
    }
}
