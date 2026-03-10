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
use File\Api\V1\Facade\FileFacade;
use App\Service\ProblemDetailsException;
use Laminas\I18n\Translator\Translator;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\Response;

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
     * @param FileFacade $fileFacade
     * @param Translator $translator
     * @param array $uploadconfig
     * @param string $encriptionkey
     */
    public function __construct(
        private readonly FileFacade $fileFacade,
        private readonly Translator $translator,
        private array $uploadConfig,
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

        $path =
            $this->uploadConfig['upload_path'] .
            DIRECTORY_SEPARATOR .
            $file->getFilename();

        if (!file_exists($path)) {
            if (file_exists($path.'.crypted')) {
                // To be retrocompatible search for different path when file was named with the suffix `.crypted`
                $path=$path.'.crypted';
            } else {
                throw new ProblemDetailsException(
                    404,
                    $this->translator->translate("File does not exists")
                );
            }
        }

        // Decrypt file
        $key = hash('sha256', $this->encriptionKey, true);
        $tempDestinationPath='tmp/'.md5($file->getFilename() . time() . random_int(0, mt_getrandmax()));
        $encryptedData = file_get_contents($path);
        $decoded = base64_decode($encryptedData, true);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($decoded, 0, $ivLength);
        $ciphertext = substr($decoded, $ivLength);
        $decryptedData = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        if (file_put_contents($tempDestinationPath, $decryptedData)) {
            $stream = new Stream(
                $tempDestinationPath
            );
        }

        $response = new Response($stream);

        // Can unlink the decrypted file
        unlink($tempDestinationPath);

        $response = $response->withHeader("Content-Type", $file->getExtension());
        $response = $response->withHeader("Content-Disposition", 'attachment');
        $response = $response->withHeader("X-Content-Type-Option", "nosniff");

        // No need for the SapiStreamEmitter, Response already emits the file.
        return $response;
    }
}
