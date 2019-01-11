<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

class FileTestCest
{
    protected $adminToken;
    protected $userToken;
    protected $user2Token;
    protected $pwIdToElim;
    protected $fileIdToElim;

    public function _before(ApiTester $I)
    {
        // get the userToken
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/auth', [
            'username' => 'user',
            'password' => 'user'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->userToken = $resp['token'];

        // get the adminToken
        $I->sendPOST('/api/auth', [
            'username' => 'admin',
            'password' => 'admin'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->adminToken = $resp['token'];

        // get the user2Token
        $I->sendPOST('/api/auth', [
            'username' => 'user2',
            'password' => 'user'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->user2Token = $resp['token'];
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
        /*
         *
         * ADMIN
         *
         */
        $I->amBearerAuthenticated($this->adminToken);

        // need to remove the content-type header (which was set to json) when sending files (uses form-data, but Phpbrowser sets it automatically)
        $I->deleteHeader('Content-Type');
        $I->sendPOST(
            '/api/v1/passwords',
            [
                'title' => 'testPass',
                'icon' => 'icon',
                'description' => 'thiswasthetest',
                'username' => 'admin',
                'password' => 'testipass',
                'url' => 'http://www.reddit.com/r/ProgrammerHumor',
                'tags' => 'zag1',
                'folder_id' => 5
            ],
            [
                'file' => codecept_data_dir('test.pdf')
            ]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass",
            "icon" => "icon",
            "folder_id" => 5,
            "description" => "thiswasthetest",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));
        $I->seeResponseMatchesJsonType([
            'fileId' => 'integer'
        ]);
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->pwIdToElim = $resp['password_id'];
        $this->fileIdToElim = $resp['fileId'];

        // get file as admin
        $I->sendGET('/api/v1/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->canSeeResponseMatchesJsonType([
            "file_id" => "integer",
            "password_id" => "integer",
            "mime_type" => "string",
            "creation_date" => "string",
            "name" => "string",
            "filename" => "string"
        ]);

        // get the file as user2
        $I->amBearerAuthenticated($this->user2Token);
        $I->sendGET('/api/v1/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->canSeeResponseMatchesJsonType([
            "file_id" => "integer",
            "password_id" => "integer",
            "mime_type" => "string",
            "creation_date" => "string",
            "name" => "string",
            "filename" => "string"
        ]);

        // get file as user
        $I->amBearerAuthenticated($this->userToken);
        $I->sendGET('/api/v1/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401
        ));

        // download file as admin
        $I->amBearerAuthenticated($this->adminToken);
        $I->sendGET('/api/v1/upload/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200

        // check if the original file and the returned file are the same
        $fileData = md5(file_get_contents('tests/_data/test.pdf'));
        $I->seeBinaryResponseEquals($fileData);

        // download file as user2
        $I->amBearerAuthenticated($this->user2Token);
        $I->sendGET('/api/v1/upload/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeBinaryResponseEquals($fileData);

        // download file as user
        $I->amBearerAuthenticated($this->userToken);
        $I->sendGET('/api/v1/upload/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401
        ));

        // delete file as user
        $I->sendDELETE('/api/v1/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401
        ));

        // delete file as user2
        $I->amBearerAuthenticated($this->user2Token);
        $I->sendDELETE('/api/v1/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401
        ));
        $I->seeResponseMatchesJsonType([
            "title" => "string",
            "type" => "string",
            "status" => "integer",
            "detail" => "string"
        ]);

        // delete file as admin
        $I->amBearerAuthenticated($this->adminToken);
        $I->sendDELETE('/api/v1/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204

        // post new file to initial password as admin
        $I->amBearerAuthenticated($this->adminToken);
        $I->sendPOST(
            '/api/v1/passwords/' . $this->pwIdToElim . '/files',
            null,
            [
                'file' => codecept_data_dir('test.pdf')
            ]
        );
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->canSeeResponseMatchesJsonType([
            "file_id" => "integer",
            "password_id" => "integer",
            "mime_type" => "string",
            "name" => "string",
            "filename" => "string"
        ]);
        $I->seeResponseContainsJson(array(
            "name" => "test.pdf",
            "password_id" => $this->pwIdToElim,
            "mime_type" => "application/pdf"
        ));
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->fileIdToElim = $resp['file_id'];

        //  post new file to initial password as user
        $I->amBearerAuthenticated($this->userToken);
        $I->sendPOST(
            '/api/v1/passwords/' . $this->pwIdToElim . '/files',
            null,
            [
                'file' => codecept_data_dir('test.pdf')
            ]
        );
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "title" => "string",
            "type" => "string",
            "status" => "integer",
            "detail" => "string"
        ]);
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401
        ));

        //  post new file to initial password as user2
        $I->amBearerAuthenticated($this->user2Token);
        $I->sendPOST(
            '/api/v1/passwords/' . $this->pwIdToElim . '/files',
            null,
            [
                'file' => codecept_data_dir('test.pdf')
            ]
        );
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401
        ));
        $I->seeResponseMatchesJsonType([
            "title" => "string",
            "type" => "string",
            "status" => "integer",
            "detail" => "string"
        ]);

        // delete password, check file is deleted too.
        $I->amBearerAuthenticated($this->adminToken);
        $I->sendDELETE('/api/v1/passwords/' . $this->pwIdToElim);

        // check if file got deleted too
        $I->sendGET('/api/v1/files/' . $this->fileIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); // 404
    }
}
