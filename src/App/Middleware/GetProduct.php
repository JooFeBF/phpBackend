<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use App\Repositories\ProductRepository;
use Slim\Exception\HttpNotFoundException;

class GetProduct
{
    public function __construct(private ProductRepository $repository)
    {
    }
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $data = $this->repository->getProductById((int) $id);

        if (!$data) {
          throw new HttpNotFoundException($request, "Product with ID $id not found");
        }

        $request = $request->withAttribute('product', $data);

        $response = $handler->handle($request);

        return $response;
    }
}

