<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Service\ProblemDetailsException;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

class ValidationMiddleware implements MiddlewareInterface
{
    /**
     *
     * @var Factory
     */
    private $inputFilterFactory;

    /**
     *
     * @var mixin
     */
    private $inputFilterSpecification;

    /**
     *
     * Constructor
     *
     * @param mixin $inputFilterSpecification
     */
    public function __construct($inputFilterSpecification)
    {
        $this->inputFilterFactory = new Factory();
        $this->inputFilterSpecification = $inputFilterSpecification;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
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
