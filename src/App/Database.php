<?php

declare(strict_types=1);

namespace App;
use PDO;

class Database
{
  public function __construct(
    private string $host,
    private int $port,
    private string $dbname,
    private string $username,
    private string $password
  ) {}
  public function getConnection() {
    $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $this->username, $this->password, [
      // Set error mode to exceptions
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    return $pdo;
  }
}
