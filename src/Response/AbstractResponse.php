<?php

namespace SMartins\Exceptions\Response;

use Illuminate\Http\JsonResponse;

abstract class AbstractResponse
{
    /**
     * The HTTP status code.
     *
     * @var int
     */
    protected $status;

    /**
     * The errors on response.
     *
     * @var \SMartins\Exceptions\JsonApi\ErrorCollection
     */
    protected $errors;

    /**
     * Create new JsonApi response passing the errors.
     *
     * @param \SMartins\Exceptions\JsonApi\ErrorCollection $errors
     *
     */
    public function __construct(ErrorHandledCollectionInterface $errors)
    {
        $this->errors = $errors;

        $this->setStatus((int) $this->errors->getStatusCode());
    }

    /**
     * Get the HTTP status code.
     *
     * @return  int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the HTTP status code.
     *
     * @param  int  $status  The HTTP status code.
     *
     * @return  self
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the errors on response.
     *
     * @return \SMartins\Exceptions\JsonApi\ErrorCollection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returns JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    abstract public function json(): JsonResponse;
}
