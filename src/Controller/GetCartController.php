<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\View\CartView;

readonly class GetCartController
{

    public function __construct(
        private CartView $cartView,
        private CartManager $cartManager,
        private ResponseFactory $responseFactory,
    ) {}

    public function get(RequestInterface $request): ResponseInterface
    {
        $cart = $this->cartManager->getCart();

        if (! $cart) {
            return $this->responseFactory->createResponse(
                ['message' => 'Cart not found'],
                404
            );
        }

        return $this->responseFactory->createResponse(
            $this->cartView->toArray($cart),
            200
        );
    }

}
