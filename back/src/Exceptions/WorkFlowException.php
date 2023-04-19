<?php

namespace App\Exceptions;

use App\Utils\ResponseCode;

class WorkFlowException extends GeneralException {

    /**
     * HttpFormException constructor.
     *
     * @param int             $statusCode
     * @param string|null     $message
     * @param \Exception|null $previous
     * @param array           $headers
     * @param int|null        $code
     */
    public function __construct(int $statusCode, string $message = null)
    {
        parent::__construct($statusCode, $message, null, array(), ResponseCode::WORKFLOW_VERSIONING_FAILED);
    }
}