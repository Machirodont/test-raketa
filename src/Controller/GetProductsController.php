<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\View\ProductsCategoryView;
use Raketa\BackendTestTask\View\ProductsView;

readonly class GetProductsController
{

    public function __construct(
        private ProductsCategoryView $productsCategoryView,
        private ResponseFactory $responseFactory,
    ) {}

    public function get(RequestInterface $request): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);

        return $this->responseFactory->createResponse(
            $this->productsCategoryView->toArray($rawRequest['category']),
            200
        );
    }

}
