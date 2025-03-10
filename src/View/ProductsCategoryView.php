<?php

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Repository\Entity\Product;
use Raketa\BackendTestTask\Repository\ProductRepository;

readonly class ProductsCategoryView
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductsView $productsView,
    ) {
    }

    public function toArray(string $category): array
    {
        return array_map(
            fn (Product $product) => $this->productsView->toArray($product),
            $this->productRepository->getByCategory($category)
        );
    }
}
