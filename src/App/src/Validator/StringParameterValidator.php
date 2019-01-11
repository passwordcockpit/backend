<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace App\Validator;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\InputFilter\InputFilter;
use App\Service\ProblemDetailsException;

class StringParameterValidator implements MiddlewareInterface
{
    /**
     *
     * @var InputFilterFactory
     */
    private $inputFilterFactory;

    /**
     *
     * @var array
     */
    private $translator;

    /**
     *
     * @param Translator $translator
     * @param InputFilterFactory $inputFilterFactory
     */
    public function __construct(
        InputFilterFactory $inputFilterFactory,
        $translator
    ) {
        $this->inputFilterFactory = $inputFilterFactory;
        $this->translator = $translator;
    }

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @throws ProblemDetailsException
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();
        // if (sizeof($queryParams) == 0) {
        //     return $handler->handle($request);
        // }
        if (sizeof($queryParams) > 0 && isset($queryParams['q'])) {
            $queryParam = $queryParams['q'];
            $inputFilter = $this->inputFilterFactory->createInputFilter([
                [
                    'name' => 'param',
                    'required' => false,
                    'filters' => [
                        [
                            'name' => 'StripTags'
                        ],
                        [
                            'name' => 'StringTrim'
                        ]
                    ]
                ]
            ]);

            $inputFilter->setData([
                'param' => $queryParam
            ]);

            if (!$inputFilter->isValid()) {
                $errors['errors'] = $this->generateInputErrorMessages(
                    $inputFilter
                );
            }

            $newPayload = $inputFilter->getValues();

            if (!empty($errors)) {
                throw new ProblemDetailsException(
                    400,
                    $this->translator->translate('Validation error'),
                    'Bad Request',
                    'https://httpstatuses.com/400',
                    $errors
                );
            }

            return $handler->handle($request->withParsedBody($newPayload));
        }
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
