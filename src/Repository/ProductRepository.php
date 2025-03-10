<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Exception;
use Raketa\BackendTestTask\Domain\ProductList;
use Raketa\BackendTestTask\Repository\Entity\Product;

class ProductRepository
{

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getByUuid(string $uuid): Product
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM products WHERE uuid = :uuid",
            ['uuid' => $uuid],
            ['uuid' => ParameterType::STRING]
        );

        if (empty($row)) {
            throw new Exception('Product not found');
        }

        return $this->make($row);
    }

    /**
     * @return Product[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getByCategory(string $category): array
    {
        return array_map(
            static fn(array $row): Product => $this->make($row),
            $this->connection->fetchAllAssociative(
                "SELECT * FROM products WHERE is_active = 1 AND category = :category",
                ['category' => $category],
                ['category' => ParameterType::STRING],
            )
        );
    }

    /**
     * @param  string[]  $uuidList
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getByUuidList(array $uuidList): ProductList
    {
        $productRecords = $this->connection->executeQuery(
            "SELECT * FROM products WHERE uuid IN (?)",
            [$uuidList],
            [ArrayParameterType::STRING],
        )->fetchAllAssociative();

        $productList = new ProductList();
        foreach ($productRecords as $record) {
            $productList->addProduct($this->make($record));
        }

        return $productList;
    }

    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }

}
