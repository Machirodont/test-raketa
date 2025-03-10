<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Exception;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Infrastructure\ConnectorFacade;

class CartManager
{

    private const CART_CAHCE_PREFIX = 'cart';

    public function __construct(
        private readonly ConnectorFacade $connectorFacade,
        private readonly LoggerInterface $logger,
    ) {}

    public function saveCart(Cart $cart): void
    {
        try {
            $this->connectorFacade->connector->set(
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
            return $this->connectorFacade->connector->get(
                $this->getCartCacheKey()
            );
        } catch (Exception $e) {
            $this->logger->error('Error');
        }

        return null;
    }

    private function getCartCacheKey(): string
    {
        return self::CART_CAHCE_PREFIX.'_'.session_id();
    }

}
