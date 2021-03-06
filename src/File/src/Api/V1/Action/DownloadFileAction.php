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
 * @SWG\Get(
 *     path="/v1/upload/files/{fileId}",
 *     tags={"File"},
 *     operationId="DownloadFileAction",
 *     summary="Download file",
 *     description="",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         name="fileId",
 *         in="path",
 *         description="File id",
 *         required=true,
 * 		   type="string"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Ok"
 *     ),
 *     security={
 *       {"bearerAuth": {}}
 *     }
 * )
 *
 */
class DownloadFileAction implements RequestHandlerInterface
{
    /**
     * @var ResourceGenerator
     */
    private $resourceGenerator;

    /**
     * @var HalResponseFactory
     */
    private $halResponseFactory;

    /**
     *
     * @var FileFacade
     */
    private $fileFacade;

    /**
     *
     * @var Translator
     */
    private $translator;

    /**
     *
     * @var array
     */
    private $uploadConfig;

    /**
     *
     * @var FileCipher
     */
    private $fileCipher;

    /**
     *
     * @var string
     */
    private $encriptionKey;

    /**
     *
     * @param ResourceGenerator $resourceGeneratorInstance
     * @param HalResponseFactory $halResponseFactory
     * @param FileFacade $fileFacade
     * @param Translator $translator
     * @param array $uploadconfig
     * @param FileCipher $fileCipher
     * @param string $encriptionkey
     */
    public function __construct(
        ResourceGenerator $resourceGeneratorInstance,
        HalResponseFactory $halResponseFactory,
        FileFacade $fileFacade,
        Translator $translator,
        $uploadConfig,
        FileCipher $fileCipher,
        $encriptionKey
    ) {
        $this->resourceGenerator = $resourceGeneratorInstance;
        $this->halResponseFactory = $halResponseFactory;
        $this->fileFacade = $fileFacade;
        $this->translator = $translator;
        $this->uploadConfig = $uploadConfig;
        $this->fileCipher = $fileCipher;
        $this->encriptionKey = $encriptionKey;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $file = $this->fileFacade->fetch($request->getAttribute("id"));
        $mimeTypeContentType = $file->getExtension();
        $mimeTypeExtension =
            $this->uploadConfig['accepted_mime_types'][$mimeTypeContentType];

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
        if (
            $this->fileCipher->decrypt(
                $path . '.' . 'crypted',
                $path . '.' . $mimeTypeExtension
            )
        ) {
            $stream = new Stream(
                $this->uploadConfig['upload_path'] .
                    DIRECTORY_SEPARATOR .
                    $file->getFileName() .
                    "." .
                    $mimeTypeExtension
            );
        }

        $response = new Response($stream);

        //can unlink the decrypted file
        unlink($path . '.' . $mimeTypeExtension);

        $response = $response->withHeader("Content-Type", $mimeTypeContentType);
        $response = $response->withHeader("Content-Disposition", 'attachment');
        $response = $response->withHeader("X-Content-Type-Option", "nosniff");

        // no need for the SapiStreamEmitter, Response already emits the file.
        return $response;
    }
}
