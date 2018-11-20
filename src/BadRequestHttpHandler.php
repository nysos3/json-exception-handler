<?php

namespace SMartins\JsonHandler;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait BadRequestHttpHandler
{
    public function badRequestHttpException(BadRequestHttpException $exception)
    {
        $statusCode = $exception->getStatusCode();

        $error = [[
            'status'    => (string)$statusCode,
            'code'      => (string)$this->getCode('bad_request'),
            'source'    => ['pointer' => $exception->getFile().':'.$exception->getLine()],
            'title'     => 'bad_request',
            'detail'    => $exception->getMessage(),
        ]];

        $this->jsonApiResponse->setStatus($statusCode);
        $this->jsonApiResponse->setErrors($error);
    }
}
