<?php

namespace SMartins\JsonHandler;

use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizationHandler
{
  public function authorizationException(AuthorizationException $exception)
  {
    $error = $this->getDefaultError();
    $error['status'] = '403';
    $error['code'] = (string) $this->getCode('authorization');
    $error['title'] = 'authorization';
    $error['detail'] = __('exception::exceptions.authorization.title');

    $error = [$error];

    $this->jsonApiResponse->setStatus(403);
    $this->jsonApiResponse->setErrors($error);
  }
}
