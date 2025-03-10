<?php

namespace Raketa\BackendTestTask\Controller;

class ResponseFactory
{
    public function createResponse(
      array $data,
      int $status
    ): JsonResponse {
        $response = new JsonResponse();
        $response
          ->withHeader('Content-Type', 'application/json; charset=utf-8')
          ->withStatus($status)
          ->getBody()
          ->write(
            json_encode(
              $data,
              JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
          );

        return $response;
    }

}