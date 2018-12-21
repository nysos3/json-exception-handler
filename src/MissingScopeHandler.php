<?php

namespace SMartins\JsonHandler;

use Laravel\Passport\Exceptions\MissingScopeException;

trait MissingScopeHandler
{
  public function missingScopeException(MissingScopeException $exception)
  {
    $meta = [];
    if (config('json-exception-handler.show_details_in_meta')) {
      $meta['__raw_error__'] = [
        'message' => $exception->getMessage(),
        'backtrace' => explode("\n", $exception->getTraceAsString()),
      ];
    }
    $error = [[
      'status' => '403',
      'code' => (string) $this->getCode('missing_scope'),
      'source' => ['pointer' => $exception->getFile() . ':' . $exception->getLine()],
      'title' => 'missing_scope',
      'detail' => $exception->getMessage(),
      'meta' => $meta,
    ]];

    $this->jsonApiResponse->setStatus(403);
    $this->jsonApiResponse->setErrors($error);
  }
}
