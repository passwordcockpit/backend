<?php


/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

/**
 * @OA\OpenApi(
 *     @OA\Server(
 *          url="{schema}://{host}{basepath}",
 *          description="OpenApi parameters",
 *          @OA\ServerVariable(
 *               serverVariable="schema",
 *               enum={"http", "https"},
 *               default="https"
 *          ),
 *          @OA\ServerVariable(
 *               serverVariable="host",
 *               default=SWAGGER_API_HOST
 *          ),
 *          @OA\ServerVariable(
 *               serverVariable="basepath",
 *               default="/api"
 *          )
 *     ),
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Password Cockpit - RESTful API Server",
 *         description="This is a sample server.",
 *         termsOfService="http://swagger.io/terms/",
 *         @OA\License(
 *             name="Apache 2.0",
 *             url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *         )
 *     ),
 *     @OA\ExternalDocumentation(
 *         description="Find out more about Swagger",
 *         url="http://swagger.io"
 *     ),
 * )
 */

/**
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="apiKey",
 *   name="Authorization",
 *   in="header"
 * )
 */

