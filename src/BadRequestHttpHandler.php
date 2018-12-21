<?php

namespace SMartins\JsonHandler;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait BadRequestHttpHandler
{
  public function badRequestHttpException(BadRequestHttpException $exception)
  {
    $statusCode = $exception->getStatusCode();
    $meta = [];
    if (config('json-exception-handler.show_details_in_meta')) {
      $meta['__raw_error__'] = [
        'message' => $exception->getMessage(),
        'backtrace' => explode("\n", $exception->getTraceAsString()),
      ];
    }
    $error = [[
      'status' => (string) $statusCode,
      'code' => (string) $this->getCode('bad_request'),
      'source' => ['pointer' => $exception->getFile() . ':' . $exception->getLine()],
      'title' => 'bad_request',
      'detail' => $exception->getMessage(),
      'meta' => $meta
    ]];

    $this->jsonApiResponse->setStatus($statusCode);
    $this->jsonApiResponse->setErrors($error);
  }
}
