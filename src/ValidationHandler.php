<?php

namespace SMartins\JsonHandler;

use Illuminate\Validation\ValidationException;
use SMartins\JsonHandler\Responses\Response;

trait ValidationHandler
{
    public function validationException(ValidationException $exception)
    {
        $response = new Response;
        $response->setMessage('The given data failed to pass validation.');
        $response->setCode(122);
        $response->setErrors($this->formattedErrors($exception));
        $response->setHttpCode(422);

        return $this->response = $response;
    }

    public function formattedErrors(ValidationException $exception)
    {
        return $this->formatErrorMessages($this->getTreatedMessages($exception));
    }

    public function getTreatedMessages($exception)
    {
        $messages = [];
        // ValidationException from \Illuminate\Foundation\Validation\ValidationRequests trait
        // used on Controller return a Illuminate\Http\JsonResponse
        if ($exception->response) {
            $messages = $this->getMessagesFromJsonResponse($exception);
        } else {
            // ValidationException from Illuminate\Validation\Validator has another
            // way to get messages
            $messages = $this->getMessagesFromValidator($exception);
        }

        return $messages;
    }

    public function getMessagesFromJsonResponse($exception)
    {
        return $exception->response->original;
    }

    public function getMessagesFromValidator($exception)
    {
        return $exception->validator->messages()->messages();
    }

    public function formatErrorMessages($messages)
    {
        $errors = [];
        foreach ($messages as $field => $message) {
            $error = [
                'code' => 12,
                'field' => $field,
                'message' => $message
            ];

            array_push($errors, $error);
        }

        return $errors;
    }
}