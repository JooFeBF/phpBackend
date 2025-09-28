<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repositories\ProductRepository;
use Valitron\Validator;

class Products
{
    public function __construct(private ProductRepository $repository)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $this->repository->getAllProducts();

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

    public function getProduct(Request $request, Response $response): Response
    {
        $product = $request->getAttribute('product');

        $body = json_encode($product);

        $response->getBody()->write($body);

        return $response;
    }

    public function createProduct(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        $v = new Validator($body);

        $v->mapFieldsRules([
            'name' => ['required'],
            'size' => ['required', 'integer', ['min', 1]],
            'description' => ['optional']
        ]);

        if (!$v->validate()) {
            $errors = $v->errors();
            $body = json_encode(['errors' => $errors]);
            $response->getBody()->write($body);
            return $response->withStatus(400);
        }

        $newProduct = $this->repository->createProduct($body);

        $body = json_encode([
            'message' => 'Product created successfully',
            'product' => $newProduct
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }

    public function updateProduct(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        $v = new Validator($body);
        $v->mapFieldsRules([
            'id' => ['required', 'integer', ['min', 1]],
            'name' => ['required'],
            'size' => ['required', 'integer', ['min', 1]],
            'description' => ['optional']
        ]);

        $updatedProduct = $this->repository->updateProduct($body);

        $body = json_encode([
            'message' => 'Product updated successfully',
            'product' => $updatedProduct
        ]);

        $response->getBody()->write($body);

        return $response;
    }

    public function deleteProduct(Request $request, Response $response): Response
    {
        $product = $request->getAttribute('product');

        $rows = $this->repository->deleteProduct($product['id']);

        $body = json_encode(['message' => 'Product deleted successfully', 'rows' => $rows]);

        $response->getBody()->write($body);

        return $response;
    }
}
