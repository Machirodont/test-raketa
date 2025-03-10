<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Infrastructure\Connector;

class CartManager
{

    private const CART_CACHE_PREFIX = 'cart';

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Connector $connector,
    ) {}

    public function saveCart(Cart $cart): void
    {
        try {
            $this->connector->set(
                $this->getCartCacheKey(),
                $cart
            );
        } catch (Exception $e) {
            $this->logger->error('Error');
        }
    }

    public function getCart(): ?Cart
    {
        try {
            $cart = $this->connector->get(
                $this->getCartCacheKey()
            );

            if(! $cart instanceof Cart) {
                throw new Exception('Cart not found');
            }

        } catch (Exception $e) {
            $this->logger->error('Error');
        }

        return null;
    }

    public function createCart(): Cart
    {
        return new Cart(
            $this->getCartCacheKey(),
            new Customer(0,'','','',''), // Логика получения Customer в примере отсутствует
            '',
            []
        );
    }

    private function getCartCacheKey(): string
    {
        return self::CART_CACHE_PREFIX.'_'.session_id();
    }

}
