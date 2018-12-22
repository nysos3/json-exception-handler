<?php

namespace SMartins\JsonHandler;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait BadRequestHttpHandler
{
  public function badRequestHttpException(BadRequestHttpException $exception)
  {
    $statusCode = $exception->getStatusCode();
    $error = $this->getDefaultError();
    $error['status'] = (string) $statusCode;
    $error['code'] = (string) $this->getCode('bad_request');
    $error['title'] = 'bad_request';
    $error['detail'] = $exception->getMessage();
    $error = [$error];

    $this->jsonApiResponse->setStatus($statusCode);
    $this->jsonApiResponse->setErrors($error);
  }
}
