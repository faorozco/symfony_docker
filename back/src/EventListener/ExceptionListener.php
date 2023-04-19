<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Utils\Constant\ResponseCode;

class ExceptionListener {
    public function onKernelException(ExceptionEvent $event) {
        $exception = $event->getThrowable();

        $response = new Response();

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $message = json_encode(array(
                'responseCode' => $exception->getCode(),
                'message' => $exception->getMessage()
            ));
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $message = json_encode(array(
                'responseCode' => ResponseCode::GENERIC_ERROR,
                'message' => $exception->getMessage()
            ));            
        }

        $response->setContent($message);
        $event->setResponse($response);
    }
}