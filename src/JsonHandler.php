<?php

namespace SMartins\JsonHandler;

use Throwable;
use SMartins\JsonHandler\Responses\JsonApiResponse;

trait JsonHandler
{
  use ValidationHandler,
  ModelNotFoundHandler,
  AuthorizationHandler,
  NotFoundHttpHandler,
  AuthenticationHandler,
  OAuthServerHandler,
  MissingScopeHandler,
  BadRequestHttpHandler;

  /**
   * Config file name.
   * @var string
   */
  public $configFile = 'json-exception-handler';

  /**
   * JsonApiResponse instance used on another traits to set response.
   * @var SMartins\JsonHandler\Responses\JsonApiResponse;
   */
  public $jsonApiResponse;

  /**
   * Receive exception instance to be used on methods.
   * @var Throwable
   */
  private $throwable;

  public function getDefaultError()
  {
    $meta = [];
    if (config($this->configFile . '.show_details_in_meta')) {
      $meta['__raw_error__'] = [
        'message' => $this->getMessage(),
        'detail' => $this->getDescription(),
        'backtrace' => explode("\n", $this->throwable->getTraceAsString()),
      ];
    }
    $error = [
      'status' => (string) $this->getStatusCode(),
      'code' => (string) $this->getCode(),
      'title' => str_replace('Throwable', '', class_basename($this->throwable)),
      'detail' => (config($this->configFile . '.show_details_in_meta')) ? $this->getMessage() : 'An unknown error occurred',
      'meta' => $meta,
    ];

    return $error;
  }

  /**
   * Set the default response on $response attribute. Get default value from
   * methods.
   */
  public function setDefaultResponse()
  {
    $error = [$this->getDefaultError()];

    $this->jsonApiResponse->setStatus($this->getStatusCode());
    $this->jsonApiResponse->setErrors($error);
  }

  /**
   * Get default message from exception.
   *
   * @return string Throwable message
   */
  public function getMessage()
  {
    return $this->throwable->getMessage();
  }

  /**
   * Mount the description with exception class, line and file.
   *
   * @return string
   */
  public function getDescription()
  {
    return class_basename($this->throwable) .
    ' line ' . $this->throwable->getLine() .
    ' in ' . $this->throwable->getFile();
  }

  /**
   * Get default http code. Check if exception has getStatusCode() methods.
   * If not get from config file.
   *
   * @return int
   */
  public function getStatusCode()
  {
    if (method_exists($this->throwable, 'getStatusCode')) {
      $httpCode = $this->throwable->getStatusCode();
    } else {
      $httpCode = config($this->configFile . '.http_code');
    }

    return $httpCode;
  }

  /**
   * Get error code. If code is empty from config file based on type.
   *
   * @param  string $type Code type from config file
   * @return int
   */
  public function getCode($type = 'default')
  {
    $code = $this->throwable->getCode();
    if (empty($this->throwable->getCode())) {
      $code = config($this->configFile . '.codes.' . $type);
    }

    return $code;
  }

  /**
   * Handle the json response. Check if exception is treated. If true call
   * the specific handler. If false set the default response to be returned.
   *
   * @param  Throwable $exception
   * @return JsonResponse
   */
  public function jsonResponse(Throwable $exception)
  {
    $this->throwable = $exception;
    $this->jsonApiResponse = new JsonApiResponse;

    if ($this->exceptionIsTreated()) {
      $this->callExceptionHandler();
    } else {
      $this->setDefaultResponse();
    }

    return response()->json(
      $this->jsonApiResponse->toArray(),
      $this->jsonApiResponse->getStatus(),
      config($this->configFile . '.additional_headers'),
      config($this->configFile . '.json_options')
    );
  }

  /**
   * Check if method to treat exception exists.
   *
   * @param  Throwable $exception The exception to be checked
   * @return bool              If method is callable
   */
  public function exceptionIsTreated()
  {
    return is_callable([$this, $this->methodName()]);
  }

  /**
   * Call the exception handler after of to check if the method exists.
   *
   * @param  Throwable $exception
   * @return void                 Call the method
   */
  public function callExceptionHandler()
  {
    $this->{$this->methodName()}($this->throwable);
  }

  /**
   * The method name is the exception name with first letter in lower case.
   *
   * @param  Throwable $exception
   * @return string               The method name
   */
  public function methodName()
  {
    return lcfirst(class_basename($this->throwable));
  }
}
