<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace File\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\Hal\HalResponseFactory;
use File\Api\V1\Facade\FileFacade;
use App\Service\ProblemDetailsException;
use Laminas\I18n\Translator\Translator;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\Response;
use Laminas\Crypt\FileCipher;

/**
 *
 * @OA\Get(
 *     path="/v1/upload/files/{fileId}",
 *     tags={"File"},
 *     operationId="DownloadFileAction",
 *     summary="Download file",
 *     description="",
 *     @OA\Parameter(
 *         name="fileId",
 *         in="path",
 *         description="File id",
 *         required=true,
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ok",
 *         @OA\JsonContent()
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 *
 */
class DownloadFileAction implements RequestHandlerInterface
{
    /**
     *
     * @param ResourceGenerator $resourceGenerator
     * @param HalResponseFactory $halResponseFactory
     * @param FileFacade $fileFacade
     * @param Translator $translator
     * @param array $uploadconfig
     * @param FileCipher $fileCipher
     * @param string $encriptionkey
     */
    public function __construct(
        private readonly ResourceGenerator $resourceGenerator,
        private readonly HalResponseFactory $halResponseFactory,
        private readonly FileFacade $fileFacade,
        private readonly Translator $translator,
        private array $uploadConfig,
        private readonly FileCipher $fileCipher,
        private readonly string $encriptionKey
    ) {
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $stream = null;
        $file = $this->fileFacade->fetch($request->getAttribute("id"));
        $mimeTypeContentType = $file->getExtension();

        $path =
            $this->uploadConfig['upload_path'] .
            DIRECTORY_SEPARATOR .
            $file->getFilename();

        if (!file_exists($path . "." . "crypted")) {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate("File does not exists")
            );
        }

        $this->fileCipher->setKey($this->encriptionKey);
        $tempDestinationPath='tmp/'.md5($file->getFilename() . time() . random_int(0, mt_getrandmax()));
        if ($this->fileCipher->decrypt(
            $path . '.' . 'crypted',
            $tempDestinationPath
        )
        ) {
            $stream = new Stream(
                $tempDestinationPath
            );
        }

        $response = new Response($stream);

        //can unlink the decrypted file
        unlink($tempDestinationPath);

        $response = $response->withHeader("Content-Type", $mimeTypeContentType);
        $response = $response->withHeader("Content-Disposition", 'attachment');
        $response = $response->withHeader("X-Content-Type-Option", "nosniff");

        // no need for the SapiStreamEmitter, Response already emits the file.
        return $response;
    }
}
