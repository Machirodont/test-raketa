<?php

namespace Raketa\BackendTestTask\Domain;

use Raketa\BackendTestTask\Repository\Entity\Product;

class ProductList
{
    private array $products = [];

    public function addProduct(Product $product): void
    {
        $this->products[$product->getUuid()] = $product;
    }

    public function getProductByUuid(string $uuid): ?Product
    {
        return $this->products[$uuid] ?? null;
    }

}