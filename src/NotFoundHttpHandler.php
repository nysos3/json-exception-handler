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
    $statusCode = $exception->getStatusCode();

    $error = $this->getDefaultError();
    $error['status'] = (string) $statusCode;
    $error['code'] = (string) $this->getCode('not_found_http');
    $error['title'] = $this->getDescription($exception);
    $error['detail'] = $this->getNotFoundMessage($exception);

    $error = [$error];

    $this->jsonApiResponse->setStatus($statusCode);
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
    $message = !empty($exception->getMessage()) ? $exception->getMessage() : class_basename($exception);
    if (basename($exception->getFile()) === 'RouteCollection.php') {
      $message = __('exception::exceptions.not_found_http.message');
    }

    return $message;
  }
}
