<?php

namespace SMartins\JsonHandler;

use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizationHandler
{
  public function authorizationException(AuthorizationException $exception)
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
      'code' => (string) $this->getCode('authorization'),
      'source' => ['pointer' => $exception->getFile() . ':' . $exception->getLine()],
      'title' => __('exception::exceptions.authorization.title'),
      'detail' => $exception->getMessage(),
      'meta' => $meta
    ]];

    $this->jsonApiResponse->setStatus(403);
    $this->jsonApiResponse->setErrors($error);
  }

  public function generateDescription($traces)
  {
    $action = '';
    foreach ($traces as $trace) {
      if ($trace['function'] === 'authorize') {
        $action = $this->extractAction($trace['args']);
        break;
      }
    }
  }

  public function extractAction($args)
  {
    $action = reset($args);

    $this->getWord($action);
  }

  public function getWords($action)
  {
    $words = explode('.', $action);
    if (!(count($words) > 1)) {
      $words = explode('-', $action);
      if (!(count($words) > 1)) {
        $words = preg_split('/(?=[A-Z])/', $action);
      }
    }
  }
}
