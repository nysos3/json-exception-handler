<?php

namespace SMartins\JsonHandler;

use Illuminate\Auth\AuthenticationException;

trait AuthenticationHandler
{
  public function authenticationException(AuthenticationException $exception)
  {
    $error = $this->getDefaultError();
    $error['status'] = '401';
    $error['code'] = (string) $this->getCode('authentication');
    $error['title'] = 'authentication';
    $error['detail'] = $exception->getMessage();

    $error = [$error];

    $this->jsonApiResponse->setStatus(401);
    $this->jsonApiResponse->setErrors($error);
  }
}
