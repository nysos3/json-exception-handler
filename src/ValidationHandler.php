<?php

namespace SMartins\JsonHandler;

use Illuminate\Validation\ValidationException;

trait ValidationHandler
{
    /**
     * Assign to response attribute the value to ValidationException.
     *
     * @param  ValidationException $exception
     */
    public function validationException(ValidationException $exception)
    {
        $this->jsonApiResponse->setStatus(422);
        $this->jsonApiResponse->setErrors($this->jsonApiFormatErrorMessages($exception));
    }

    /**
     * Get formatted errors on standard code, field, message to each field with
     * error.
     *
     * @param  ValidationException $exception
     * @return array
     */
    public function formattedErrors(ValidationException $exception)
    {
        return $this->jsonApiFormatErrorMessages($exception);
    }

    public function jsonApiFormatErrorMessages(ValidationException $exception)
    {
        $validationMessages = $this->getTreatedMessages($exception);
        $validationFails = $this->getTreatedFails($exception);

        $errors = [];
        foreach ($validationMessages as $field => $messages) {
            foreach ($messages as $key => $message) {
                $attributes = $this->getValidationAttributes($validationFails, $key, $field);
                $path = str_replace('/', ':', request()->path());
                $error = [
                    'status'    => '422',
                    'code'      => "V:{$path}::{$field}",
                    'source'    => ['parameter' => $field],
                    'title'     => $attributes['title'],
                    'detail'    => $message,
                ];
                array_push($errors, $error);
            }
        }

        return $errors;
    }

    public function getValidationAttributes(array $validationFails, $key, $field)
    {
        return [
            'code' => (string)$this->getValidationCode($validationFails, $key, $field),
            'title' => $this->getValidationTitle($validationFails, $key, $field),
        ];
    }

    public function getValidationTitle(array $validationFails, $key, $field)
    {
        return __('exception::exceptions.validation.title', [
            'fails' => array_keys($validationFails[$field])[$key],
            'field' => $field,
        ]);
    }

    public function getValidationCode(array $validationFails, $key, $field)
    {
        $rule = strtolower(array_keys($validationFails[$field])[$key]);

        return config($this->configFile.'.codes.validation_fields.'.$field.'.'.$rule);
    }

    /**
     * Get message based on exception type. If exception is generated by
     * $this->validate() from default Controller methods the exception has the
     * response object. If exception is generated by Validator::make() the
     * messages are getted different.
     *
     * @param  Exception $exception
     * @return array
     */
    public function getTreatedMessages($exception)
    {
        return $this->getMessagesFromValidator($exception);
    }

    public function getMessagesFromValidator($exception)
    {
        return $exception->validator->messages()->messages();
    }

    public function getTreatedFails($exception)
    {
        return $this->getFailsFromValidator($exception);
    }

    public function getFailsFromValidator($exception)
    {
        return $exception->validator->failed();
    }
}
