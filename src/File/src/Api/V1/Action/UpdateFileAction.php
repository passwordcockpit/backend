<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace File\Api\V1\Action;

use Doctrine\ORM\EntityManager;
use Psr\Http\Server\RequestHandlerInterface;
use File\Api\V1\Facade\FileFacade;
use Password\Api\V1\Entity\Password;
use Password\Api\V1\Facade\PasswordFacade;
use App\Service\ProblemDetailsException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\Hal\HalResponseFactory;
use Laminas\Crypt\FileCipher;
use File\Api\V1\Entity\File;

/**
 * @OA\Post(
 *     path="/v1/passwords/{passwordId}/files",
 *     summary="Create a file for the specific password",
 *     description="",
 *     operationId="updateFile",
 *     tags={"passwords"},
 *     @OA\Parameter(
 *         description="password id to which the file is associated",
 *         in="path",
 *         name="passwordId",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         description="file to upload",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(type="file")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Password not found"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */

class UpdateFileAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param FileFacade $fileFacade
     * @param PasswordFacade $passwordFacade
     * @param array $uploadConfig
     * @param Translator $translator
     * @param EntityManager $entityManager
     * @param FileCipher $fileCipher
     * @param string $encriptionkey
     * @param ResourceGenerator $resourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        protected FileFacade $fileFacade,
        protected PasswordFacade $passwordFacade,
        private array $uploadConfig,
        private $translator, private $entityManager,
        private readonly FileCipher $fileCipher,
        private string $encriptionKey,
        private readonly ResourceGenerator $resourceGenerator,
        private readonly HalResponseFactory $halResponseFactory
    ){}

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // get password
        $passId = $request->getAttribute('id');
        $password = $this->entityManager
            ->getRepository(Password::class)
            ->find($passId);
        if (!isset($password)) {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Password not found')
            );
        }

        // check if password already have a file
        $file = $this->entityManager
            ->getRepository(File::class)
            ->findBy(['password' => $password]);
        if ($file != null) {
            throw new ProblemDetailsException(
                400,
                $this->translator->translate('Password already has a file')
            );
        }

        // Check if file was uploaded
        if (isset($request->getUploadedFiles()['file'])) {
            $file = $request->getUploadedFiles()['file'];

            // handle physical file
            $file = $this->fileFacade->handleFile(
                $file,
                $this->uploadConfig,
                $this->fileCipher,
                $this->encriptionKey,
                $password
            );

            $password->setFileId($file->getFileId());
            $password->setFileName($file->getName());

            $resource = $this->resourceGenerator->fromObject($file, $request);
            return $this->halResponseFactory->createResponse(
                $request,
                $resource
            );
        } else {
            throw new ProblemDetailsException(
                400,
                $this->translator->translate('File not found')
            );
        }
    }
}
