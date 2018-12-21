<?php

namespace SMartins\JsonHandler;

use League\OAuth2\Server\Exception\OAuthServerException;

trait OAuthServerHandler
{
  public function oAuthServerException(OAuthServerException $exception)
  {
    $statusCode = $exception->getHttpStatusCode();
    $meta = [];
    if (config('json-exception-handler.show_details_in_meta')) {
      $meta['__raw_error__'] = [
        'message' => $exception->getMessage(),
        'backtrace' => explode("\n", $exception->getTraceAsString()),
      ];
    }

    $error = [[
      'status' => (string) $statusCode,
      'code' => (string) $this->getCode('not_found_http'),
      'source' => ['pointer' => $exception->getFile() . ':' . $exception->getLine()],
      'title' => $exception->getErrorType(),
      'detail' => $exception->getMessage(),
      'meta' => $meta
    ]];

    $this->jsonApiResponse->setStatus($statusCode);
    $this->jsonApiResponse->setErrors($error);
  }
}
