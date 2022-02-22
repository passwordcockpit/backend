<?php

/**
 * ErrorResponseGenerator
 *
 * @package App\Service
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Aron Castellani <aron.castellani@blackpoints.ch>
 */

namespace App\Service;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;

class ErrorResponseGenerator
{
    public function __construct(private readonly ProblemDetailsResponseFactory $problemDetailsFactory)
    {
    }

    /**
     * Final handler for an application.
     *
     * @param ServerRequestInterface $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(
        $e,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        return $this->problemDetailsFactory->createResponseFromThrowable(
            $request,
            $e
        );
    }
}
