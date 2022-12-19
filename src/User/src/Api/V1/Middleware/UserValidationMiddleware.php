<?php

/**
 * UserValidationMiddleware
 *
 * @package App\Middleware
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Aron Castellani <aron.castellani@blackpoints.ch>
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace User\Api\V1\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Service\ProblemDetailsException;
use Laminas\I18n\Translator\Translator;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\Factory as InputFilterFactory;
use User\Api\V1\Entity\User;

class UserValidationMiddleware implements MiddlewareInterface
{
    /**
     * Constructor
     *
     * @param InputFilterFactory $inputFilterfactory
     * @param array $languages
     * @param Translator $translator
     * @param bool $update
     */
    public function __construct(
        private readonly InputFilterFactory $inputFilterFactory,
        private array $languages,
        private Translator $translator,
        private bool $update = false
    ){}

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

        // zend filter for booolean value does not allow NULL to go through, even with value.
        $enabled = false;
        if (!isset($payload['enabled']) && $this->update) {
            $enabled = true;
        }

        $exclude = [];
        if ($request->getAttribute('id')) {
            $userId = $request->getAttribute('id');
            $exclude = [
                'field' => 'userId',
                'value' => $userId
            ];
        }

        $inputFilterSpecification = [
            [
                'name' => 'username',
                'required' => !$this->update,
                'continue_if_empty' => $this->update,
                'filters' => [
                    [
                        'name' => \Laminas\Filter\StringTrim::class,
                        'options' => [
                            'charlist' => ": "
                        ],
                        'charlist' => ' '
                    ],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'min' => 2,
                            'max' => 45
                        ]
                    ],
                    [
                        'name' => \App\Validator\NoEntityExists::class,
                        'options' => [
                            'entity' => User::class,
                            'field' => 'username',
                            'exclude' => $exclude
                        ]
                    ]
                ]
            ],
            [
                'name' => 'password',
                'required' =>!$this->update && getenv('PASSWORDCOCKPIT_AUTHENTICATION_TYPE')!=='ldap',
                'continue_if_empty' => true,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'min' => 8,
                            'max' => 200
                        ]
                    ],
                    [
                        'name' => \Laminas\Validator\Regex::class,
                        'options' => [
                            'pattern' =>
                                '/^\S*(?=\S*[\W])(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'name',
                'required' => !$this->update,
                'continue_if_empty' => true,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 45
                        ]
                    ]
                ]
            ],
            [
                'name' => 'surname',
                'required' => !$this->update,
                'continue_if_empty' => true,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 45
                        ]
                    ]
                ]
            ],
            [
                'name' => 'email',
                'required' => !$this->update,
                'continue_if_empty' => $this->update,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'max' => 45
                        ]
                    ],
                    [
                        'name' => \Laminas\Validator\EmailAddress::class
                    ],
                    [
                        'name' => \App\Validator\NoEntityExists::class,
                        'options' => [
                            'entity' => User::class,
                            'field' => 'email',
                            'exclude' => $exclude
                        ]
                    ]
                ]
            ],
            [
                'name' => 'language',
                'required' => !$this->update,
                'continue_if_empty' => $this->update,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'min' => 2,
                            'max' => 2
                        ]
                    ],
                    [
                        'name' => \Laminas\Validator\InArray::class,
                        'options' => [
                            'haystack' => $this->languages
                        ]
                    ]
                ]
            ],
            [
                'name' => 'phone',
                'required' => false,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 45
                        ]
                    ],
                    [
                        'name' => \Laminas\Validator\Regex::class,
                        'options' => [
                            'pattern' => '/^[\d-]+$/'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'enabled',
                'required' => !$this->update,
                'allow_empty' => false,
                'continue_if_empty' => true,
                'filters' => [
                    [
                        'name' => \Laminas\Filter\Boolean::class,
                        'options' => [
                            'casting' => false,
                            'type' => [
                                \Laminas\Filter\Boolean::TYPE_NULL,
                                \Laminas\Filter\Boolean::TYPE_BOOLEAN,
                                \Laminas\Filter\Boolean::TYPE_INTEGER
                            ]
                        ]
                    ]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Callback::class,
                        'options' => [
                            'callback' => fn($value) => is_bool($value)
                        ]
                    ]
                ]
            ]
        ];

        $inputFilter = $this->inputFilterFactory->createInputFilter(
            $inputFilterSpecification
        );

        if(getenv('PASSWORDCOCKPIT_AUTHENTICATION_TYPE')==='ldap'){
            // Remove password field
            unset($payload['password']);
            $inputFilter->remove('password');
        }

        $inputFilter->setData($payload);

        if (!$inputFilter->isValid()) {
            $errors['errors'] = $this->generateInputErrorMessages($inputFilter);
        }

        if (!empty($errors)) {
            throw new ProblemDetailsException(
                400,
                $this->translator->translate('Validation error'),
                'Bad Request',
                'https://httpstatuses.com/400',
                $errors
            );
        }

        $filteredParams = $inputFilter->getValues(); // only filtered values
        $newPayload = array_merge($payload, $filteredParams); // merge of original payload with filteredParams

        // putting back 'enabled' to null if it did not exist in the beginning.
        if ($enabled) {
            $newPayload['enabled'] = null;
        }

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
