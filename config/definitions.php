<?php

use App\Database;

$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->load();

return [
  Database::class => function() {
    return new Database(
      host: $_ENV["DATABASE_HOSTNAME"],
      port: $_ENV["DATABASE_PORT"],
      dbname: $_ENV["DATABASE_NAME"],
      username: $_ENV["DATABASE_USERNAME"],
      password: $_ENV["DATABASE_PASSWORD"]
    );
  }
];
