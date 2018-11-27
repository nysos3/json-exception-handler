<?php

namespace SMartins\JsonHandler;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait NotFoundHttpHandler
{
    /**
     * Set response parameters to NotFoundHttpException.
     *
     * @param  NotFoundHttpException $exception
     */
    public function notFoundHttpException(NotFoundHttpException $exception)
    {
        $statuCode = $exception->getStatusCode();
        $error = [[
            'status' => (string)$statuCode,
            'code'   => (string)$this->getCode('not_found_http'),
            'source' => ['pointer' => request()->path()],
            'title'  => $this->getDescription($exception),
            'detail' => $this->getNotFoundMessage($exception),
        ]];

        $this->jsonApiResponse->setStatus($statuCode);
        $this->jsonApiResponse->setErrors($error);
    }

    /**
     * Get message based on file. If file is RouteCollection return specific
     * message.
     *
     * @param  NotFoundHttpException $exception
     * @return string
     */
    public function getNotFoundMessage(NotFoundHttpException $exception)
    {
        $message = ! empty($exception->getMessage()) ? $exception->getMessage() : class_basename($exception);
        if (basename($exception->getFile()) === 'RouteCollection.php') {
            $message = __('exception::exceptions.not_found_http.message');
        }

        return $message;
    }
}
