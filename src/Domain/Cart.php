<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain;

final class Cart
{

    /**
     * @param  string  $uuid
     * @param  \Raketa\BackendTestTask\Domain\Customer  $customer
     * @param  string  $paymentMethod
     * @param  CartItem[]  $items
     */
    public function __construct(
        readonly private string $uuid,
        readonly private Customer $customer,
        readonly private string $paymentMethod,
        private array $items,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(CartItem $item): void
    {
        $this->items[] = $item;
    }

    public function getItemsUuidList(): array
    {
        return array_map(
            fn(CartItem $item) => $item->getUuid(),
            $this->items
        );
    }

}
