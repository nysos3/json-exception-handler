<?php

namespace SMartins\JsonHandler;

use Illuminate\Auth\AuthenticationException;

trait AuthenticationHandler
{
  public function authenticationException(AuthenticationException $exception)
  {
    $meta = [];
    if (config('json-exception-handler.show_details_in_meta')) {
      $meta['__raw_error__'] = [
        'message' => $exception->getMessage(),
        'backtrace' => explode("\n", $exception->getTraceAsString()),
      ];
    }
    $error = [[
      'status' => '401',
      'code' => (string) $this->getCode('authentication'),
      'source' => ['pointer' => $exception->getFile() . ':' . $exception->getLine()],
      'title' => $exception->getMessage(),
      'detail' => __('exception::exceptions.authentication.detail'),
      'meta' => $meta
    ]];

    $this->jsonApiResponse->setStatus(401);
    $this->jsonApiResponse->setErrors($error);
  }
}
