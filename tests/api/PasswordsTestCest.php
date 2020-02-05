<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

use Codeception\Lib\Connector\ZendExpressive;

class PasswordsTestCest
{
    private $userToken;
    private $adminToken;

    private $pwIdToElim;

    public function _before(ApiTester $I)
    {
        // get the userToken
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/auth', [
            'username' => 'user',
            'password' => 'User123!'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->userToken = $resp['token'];

        // get the adminToken
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/auth', [
            'username' => 'admin',
            'password' => 'Admin123!'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->adminToken = $resp['token'];
    }

    // tests, divided by admin trying on passwords on folder where he has manage, then read, then nothing
    // then the same for user
    public function tryToTest(ApiTester $I)
    {
        //---------ADMIN-----------
        $I->amBearerAuthenticated($this->adminToken);

        // ----------MANAGE-PERMISSION-----------

        // list password logs of specific password under a folder where he has MANAGE
        $I->sendGET('/api/v1/passwords/6/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "logs" => "array"
            ]
        ]);

        // list password files of specific password under a folder where he has MANAGE
        $I->sendGET('/api/v1/passwords/6/files');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        // $fil = new Laminas\Diactoros\UploadedFile(
        //     codecept_data_dir('test.pdf'),
        //     filesize(codecept_data_dir('test.pdf')),
        //     0
        // );

        // create a password with right params on folder he has manage on
        // TODO: SEND ALSO THE FILE
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
                'folder_id' => 2
            ]
            //[
            //$fil
            //'file' => codecept_data_dir('test.pdf')
            // 'file' => [
            //     'name' => 'test.pdf',
            //     'type' => 'application/pdf',
            //     'error' => 0,
            //     'size' => filesize(codecept_data_dir('test.pdf')),
            //     'tmp_name' => codecept_data_dir('test.pdf')
            // ]
            //]
        );
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass",
            "icon" => "icon",
            "folder_id" => 2,
            "description" => "thiswasthetest",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->pwIdToElim = $resp['password_id'];

        //get this new password
        $I->sendGET('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass",
            "icon" => "icon",
            "folder_id" => 2,
            "description" => "thiswasthetest",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));

        //admin update password
        $I->sendPATCH('/api/v1/passwords/' . $this->pwIdToElim, [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'folder_id' => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass11",
            "icon" => "icon",
            "folder_id" => 2,
            "description" => "thiswasthetest11",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));

        // send update with wrong data
        $I->sendPATCH('/api/v1/passwords/' . $this->pwIdToElim, [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'reddit.com/r/ProgrammerHumor',
            'folder_id' => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();

        // delete password
        $I->sendDELETE('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204

        // deleting it again -> not found!
        $I->sendDELETE('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); // 404
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'title' => 'string',
            'type' => 'string',
            'status' => 'integer',
            'detail' => 'string'
        ]);
        $I->seeResponseContainsJson(array(
            'title' => 'Risorsa non trovata',
            'status' => 404
        ));

        // ----------READ-PERMISSION-----------

        // list password logs of specific password under a folder where he has READ
        $I->sendGET('/api/v1/passwords/1/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "logs" => "array"
            ]
        ]);

        // list password files of specific password under a folder where he has READ
        $I->sendGET('/api/v1/passwords/1/files');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        //admin send create password on folder he has read on
        $I->sendPOST('/api/v1/passwords', [
            'title' => 'testPass',
            'icon' => 'icon',
            'description' => 'thiswasthetest',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'tags' => 'zag1',
            'folder_id' => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass",
            "icon" => "icon",
            "folder_id" => 1,
            "description" => "thiswasthetest",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->pwIdToElim = $resp['password_id'];

        // wrong password data
        $I->sendPOST('/api/v1/passwords', [
            'icon' => 'icon',
            'description' => 'thiswasthetest',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'nope',
            'tags' => 'zag1',
            'folder_id' => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST); // 400
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'errors' => 'array'
        ]);
        $I->seeResponseContainsJson(array(
            'title' => 'Bad Request',
            'status' => 400
        ));

        //get this new password
        $I->sendGET('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass",
            "icon" => "icon",
            "folder_id" => 1,
            "description" => "thiswasthetest",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));

        //admin update password
        $I->sendPATCH('/api/v1/passwords/' . $this->pwIdToElim, [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'folder_id' => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass11",
            "icon" => "icon",
            "folder_id" => 1,
            "description" => "thiswasthetest11",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));

        // send update with correct data
        $I->sendPATCH('/api/v1/passwords/' . $this->pwIdToElim, [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'reddit.com/r/ProgrammerHumor',
            'folder_id' => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();

        // delete password
        $I->sendDELETE('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204

        // deleting it again -> not found!
        $I->sendDELETE('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); // 404
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'title' => 'string',
            'type' => 'string',
            'status' => 'integer',
            'detail' => 'string'
        ]);
        $I->seeResponseContainsJson(array(
            'title' => 'Risorsa non trovata',
            'status' => 404
        ));

        // ---------NO-PERMISSIONS-------------
        // (remember we are admin now)

        // list password logs of specific password under a folder where he does NOT have permissions
        $I->sendGET('/api/v1/passwords/2/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "logs" => "array"
            ]
        ]);

        // list password files of specific password under a folder where he does NOT have permissions
        $I->sendGET('/api/v1/passwords/2/files');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        //admin send create password on folder he has no permissions on
        $I->sendPOST('/api/v1/passwords', [
            'title' => 'testPass',
            'icon' => 'icon',
            'description' => 'thiswasthetest',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'tags' => 'zag1',
            'folder_id' => 3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass",
            "icon" => "icon",
            "folder_id" => 3,
            "description" => "thiswasthetest",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->pwIdToElim = $resp['password_id'];

        // wrong password data
        $I->sendPOST('/api/v1/passwords', [
            'icon' => 'icon',
            'description' => 'thiswasthetest',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'nope',
            'tags' => 'zag1',
            'folder_id' => 3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST); // 400
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'errors' => 'array'
        ]);
        $I->seeResponseContainsJson(array(
            'title' => 'Bad Request',
            'status' => 400
        ));

        //get this new password
        $I->sendGET('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass",
            "icon" => "icon",
            "folder_id" => 3,
            "description" => "thiswasthetest",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));

        //admin update password
        $I->sendPATCH('/api/v1/passwords/' . $this->pwIdToElim, [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'folder_id' => 3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass11",
            "icon" => "icon",
            "folder_id" => 3,
            "description" => "thiswasthetest11",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));

        // send update
        $I->sendPUT('/api/v1/passwords/' . $this->pwIdToElim, [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'reddit.com/r/ProgrammerHumor',
            'folder_id' => 3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();

        // delete password
        $I->sendDELETE('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204

        // deleting it again -> not found!
        $I->sendDELETE('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); // 404
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'title' => 'string',
            'type' => 'string',
            'status' => 'integer',
            'detail' => 'string'
        ]);
        $I->seeResponseContainsJson(array(
            'title' => 'Risorsa non trovata',
            'status' => 404
        ));

        // ---------USER-------------

        /*
         *
         *
         */

        $I->amBearerAuthenticated($this->userToken);

        // ----------MANAGE-PERMISSION-----------

        // list password logs of specific password under a folder where he has MANAGE
        $I->sendGET('/api/v1/passwords/2/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // list password files of specific password under a folder where he has MANAGE
        $I->sendGET('/api/v1/passwords/2/files');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        // create a password with right params on folder he has manage on
        // TODO: SEND ALSO THE FILE
        $I->sendPOST('/api/v1/passwords', [
            'title' => 'testPass',
            'icon' => 'icon',
            'description' => 'thiswasthetest',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'tags' => 'zag1',
            'folder_id' => 3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass",
            "icon" => "icon",
            "folder_id" => 3,
            "description" => "thiswasthetest",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->pwIdToElim = $resp['password_id'];

        //get this new password
        $I->sendGET('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass",
            "icon" => "icon",
            "folder_id" => 3,
            "description" => "thiswasthetest",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));

        //user update password
        $I->sendPATCH('/api/v1/passwords/' . $this->pwIdToElim, [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'folder_id' => 3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "testPass11",
            "icon" => "icon",
            "folder_id" => 3,
            "description" => "thiswasthetest11",
            "username" => "admin",
            "password" => "testipass",
            "url" => "http://www.reddit.com/r/ProgrammerHumor",
            "tags" => "zag1"
        ));

        // delete password
        $I->sendDELETE('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204

        // deleting it again -> not found!
        $I->sendDELETE('/api/v1/passwords/' . $this->pwIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); // 404
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'title' => 'string',
            'type' => 'string',
            'status' => 'integer',
            'detail' => 'string'
        ]);
        $I->seeResponseContainsJson(array(
            'title' => 'Risorsa non trovata',
            'status' => 404
        ));

        // ----------READ-PERMISSION-----------

        // list password logs of specific password under a folder where he has READ
        $I->sendGET('/api/v1/passwords/6/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // list password files of specific password under a folder where he has READ
        $I->sendGET('/api/v1/passwords/6/files');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        //user send create password on folder he has read on
        $I->sendPOST('/api/v1/passwords', [
            'title' => 'testPass',
            'icon' => 'icon',
            'description' => 'thiswasthetest',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'tags' => 'zag1',
            'folder_id' => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare POST su questa risorsa"
        ));

        // wrong password data
        $I->sendPOST('/api/v1/passwords', [
            'icon' => 'icon',
            'description' => 'thiswasthetest',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'nope',
            'tags' => 'zag1',
            'folder_id' => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare POST su questa risorsa"
        ));

        //get a password from folder where user has read on
        $I->sendGET('/api/v1/passwords/6');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "folder_id" => 2
        ));

        //user update password
        $I->sendPATCH('/api/v1/passwords/6', [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'folder_id' => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare PATCH su questa risorsa"
        ));

        // send update with wrong data
        $I->sendPUT('/api/v1/passwords/6', [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'reddit.com/r/ProgrammerHumor',
            'folder_id' => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare PUT su questa risorsa"
        ));

        // delete password
        $I->sendDELETE('/api/v1/passwords/6');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" =>
                "Utente user non può effettuare DELETE su questa risorsa"
        ));

        // ---------NO-PERMISSIONS-------------
        // (user with no permissions on folder)

        // list password logs of specific password under a folder where he does NOT have permissions
        $I->sendGET('/api/v1/passwords/1/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // list password files of specific password under a folder where he does NOT have permissions
        $I->sendGET('/api/v1/passwords/1/files');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        //user send create password on folder he has no permissions on
        $I->sendPOST('/api/v1/passwords', [
            'title' => 'testPass',
            'icon' => 'icon',
            'description' => 'thiswasthetest',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'tags' => 'zag1',
            'folder_id' => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare POST su questa risorsa"
        ));

        // wrong password data
        $I->sendPOST('/api/v1/passwords', [
            'icon' => 'icon',
            'description' => 'thiswasthetest',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'nope',
            'tags' => 'zag1',
            'folder_id' => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare POST su questa risorsa"
        ));

        //get this new password
        $I->sendGET('/api/v1/passwords/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        //user update password
        $I->sendPATCH('/api/v1/passwords/1', [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'http://www.reddit.com/r/ProgrammerHumor',
            'folder_id' => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare PATCH su questa risorsa"
        ));

        // send update with wrong data
        $I->sendPUT('/api/v1/passwords/1', [
            'title' => 'testPass11',
            'description' => 'thiswasthetest11',
            'username' => 'admin',
            'password' => 'testipass',
            'url' => 'reddit.com/r/ProgrammerHumor',
            'folder_id' => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare PUT su questa risorsa"
        ));

        // delete password
        $I->sendDELETE('/api/v1/passwords/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" =>
                "Utente user non può effettuare DELETE su questa risorsa"
        ));
    }
}
