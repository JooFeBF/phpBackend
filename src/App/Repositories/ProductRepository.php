<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class ProductRepository
{
    public function __construct(private Database $database)
    {
    }

    public function getAllProducts(): array
    {
      // Database connection parameters
      $pdo = $this->database->getConnection();
      $stmt = $pdo->query('SELECT * FROM product');

      // Fetch all results as an associative array
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $data;
    }

    public function getProductById(int $id): ?array
    {
      $pdo = $this->database->getConnection();
      // We use a placeholder to prevent SQL injection
      $sql = 'SELECT * FROM product WHERE id = :id';

      $stmt = $pdo->prepare($sql);

      $stmt->bindParam(':id', $id, PDO::PARAM_INT);

      $stmt->execute(['id' => $id]);

      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      return $data ?: null;
    }

    public function createProduct(array $product): int
    {
      $pdo = $this->database->getConnection();

      $sql = 'INSERT INTO product (name, description, size) VALUES (:name, :description, :size)';

      $stmt = $pdo->prepare($sql);

      $stmt->bindValue(':name', $product['name'], PDO::PARAM_STR);

      $stmt->bindValue(':size', $product['size'], PDO::PARAM_INT);

      if (isset($product['description'])) {
          $stmt->bindValue(':description', $product['description'], PDO::PARAM_STR);
      } else {
          $stmt->bindValue(':description', null, PDO::PARAM_NULL);
      }

      $stmt->execute();

      return (int)$pdo->lastInsertId();
    }

      public function updateProduct(array $product): ?array
      {
      $pdo = $this->database->getConnection();

      $sql = 'UPDATE product SET name = :name, description = :description, size = :size WHERE id = :id';

      $stmt = $pdo->prepare($sql);

      $stmt->bindValue(':name', $product['name'], PDO::PARAM_STR);

      if (isset($product['description'])) {
          $stmt->bindValue(':description', $product['description'], PDO::PARAM_STR);
      } else {
          $stmt->bindValue(':description', null, PDO::PARAM_NULL);
      }

      $stmt->bindValue(':id', $product['id'], PDO::PARAM_INT);

      $stmt->bindValue(':size', $product['size'], PDO::PARAM_INT);

      $stmt->execute();

      $updatedProduct = $this->getProductById((int) $product['id']);

      return $updatedProduct;
    }

    public function deleteProduct(int $id)
    {
      $pdo = $this->database->getConnection();

      $sql = 'DELETE FROM product WHERE id = :id';

      $stmt = $pdo->prepare($sql);

      $stmt->bindValue(':id', $id, PDO::PARAM_INT);

      $stmt->execute();
    }

}
