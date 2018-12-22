<?php

namespace SMartins\JsonHandler;

use League\OAuth2\Server\Exception\OAuthServerException;

trait OAuthServerHandler
{
  public function oAuthServerException(OAuthServerException $exception)
  {
    $statusCode = $exception->getHttpStatusCode();

    $error = $this->getDefaultError();
    $error['status'] = (string) $statusCode;
    $error['code'] = (string) $this->getCode('oauth_server');
    $error['title'] = 'authenticating';
    $error['detail'] = $exception->getErrorType();

    $error = [$error];

    $this->jsonApiResponse->setStatus($statusCode);
    $this->jsonApiResponse->setErrors($error);
  }
}
