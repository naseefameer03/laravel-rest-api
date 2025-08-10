<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    // write codes to handle API exceptions
    protected $message;
    protected $code;
    protected $statusCode;
    protected $errors;

    public function __construct($message = 'An error occurred', $code = 0, $statusCode = 500, $errors = [])
    {
        parent::__construct($message, $code);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getErrors()
    {
        return $this->errors;
    }
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => $this->getErrors(),
        ], $this->getStatusCode());
    }
    public function report()
    {
        // Log the exception or perform any other reporting logic
        \Log::error($this->getMessage(), [
            'code' => $this->getCode(),
            'status_code' => $this->getStatusCode(),
            'errors' => $this->getErrors(),
        ]);
    }
    public function __toString()
    {
        return sprintf(
            "ApiException [Code: %s, Status Code: %s]: %s",
            $this->getCode(),
            $this->getStatusCode(),
            $this->getMessage()
        );
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;   
    }
}
