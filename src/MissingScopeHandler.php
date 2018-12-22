<?php

namespace SMartins\JsonHandler;

use Laravel\Passport\Exceptions\MissingScopeException;

trait MissingScopeHandler
{
  public function missingScopeException(MissingScopeException $exception)
  {
    $error = $this->getDefaultError();
    $error['status'] = '403';
    $error['code'] = (string) $this->getCode('missing_scope');
    $error['title'] = 'missing_scope';
    $error['detail'] = $exception->getMessage();

    $error = [$error];

    $this->jsonApiResponse->setStatus(403);
    $this->jsonApiResponse->setErrors($error);
  }
}
