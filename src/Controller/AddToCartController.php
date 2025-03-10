<?php

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Raketa\BackendTestTask\View\CartView;
use Ramsey\Uuid\Uuid;

readonly class AddToCartController
{

    public function __construct(
      private ProductRepository $productRepository,
      private CartView $cartView,
      private CartManager $cartManager,
      private ResponseFactory $responseFactory,
    ) {}

    public function post(RequestInterface $request): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);
        $product = $this->productRepository->getByUuid(
          $rawRequest['productUuid']
        );

        if(!$product->isActive()){
            return $this->responseFactory->createResponse(
                ['message' => 'Product not available'],
                409
            );
        }

        $cart = $this->cartManager->getCart();
        if (! $cart) {
            $cart = $this->cartManager->createCart();
        }

        $cart->addItem(
          new CartItem(
            Uuid::uuid4()->toString(),
            $product->getUuid(),
            $product->getPrice(),
            $rawRequest['quantity'],
          )
        );

        $this->cartManager->saveCart($cart);

        return $this->responseFactory->createResponse(
          [
            'status' => 'success',
            'cart' => $this->cartView->toArray($cart),
          ],
          200
        );
    }

}
