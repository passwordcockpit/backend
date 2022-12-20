<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Service\ProblemDetailsException;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilter;

class ValidationMiddleware implements MiddlewareInterface
{
    private readonly \Laminas\InputFilter\Factory $inputFilterFactory;

    /**
     *
     * Constructor
     *
     * @param mixin $inputFilterSpecification
     */
    public function __construct(private $inputFilterSpecification)
    {
        $this->inputFilterFactory = new Factory();
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $errors = [];
        $payload = $request->getParsedBody();
        $inputFilter = $this->inputFilterFactory->createInputFilter(
            $this->inputFilterSpecification
        );
        $inputFilter->setData($payload);

        if (!$inputFilter->isValid()) {
            $errors['errors'] = $this->generateInputErrorMessages($inputFilter);
        }
        if (!empty($errors)) {
            throw new ProblemDetailsException(
                400,
                'Validation error',
                'Bad Request',
                'https://httpstatuses.com/400',
                $errors
            );
        }

        $filteredParams = $inputFilter->getValues(); // get filtered values
        $newPayload = array_merge($payload, $filteredParams); // merge of original payload with filteredParams
        $request = $request->withParsedBody($newPayload);

        return $handler->handle($request);
    }

    /**
     * Extract errors from input filter
     *
     * @param InputFilter $inputFilter
     * @return mixin
     */
    private function generateInputErrorMessages($inputFilter)
    {
        $errors = [];
        foreach ($inputFilter->getInvalidInput() as $error) {
            $msgs = [];
            foreach ($error->getMessages() as $msg) {
                $msgs[] = $msg;
            }
            $errors[] = [
                "name" => $error->getName(),
                "value" => $error->getValue(),
                "messages" => $msgs
            ];
        }
        return $errors;
    }
}
