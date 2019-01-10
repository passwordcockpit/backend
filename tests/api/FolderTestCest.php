<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

class FolderTestCest
{
    protected $adminToken;
    protected $userToken;
    protected $user2Token;

    protected $fldIdToElim;

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
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/auth', [
            'username' => 'admin',
            'password' => 'admin'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->adminToken = $resp['token'];
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
        /*
         *
         *
         *
         *
         *
         */
        //---------ADMIN-----------
        $I->amBearerAuthenticated($this->adminToken);

        // GET FOLDERS
        $I->sendGET('/api/v1/folders');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "folders" => "array"
            ]
        ]);

        // GET SPECIFIC FOLDER
        // read
        $I->sendGET('/api/v1/folders/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "folder_id" => 1,
            "parent_id" => null,
            "name" => "folder",
            "access" => 1
        ));

        // manage
        $I->sendGET('/api/v1/folders/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "folder_id" => 2,
            "parent_id" => 1,
            "name" => "fld2",
            "access" => 2
        ));

        // null
        $I->sendGET('/api/v1/folders/3');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "folder_id" => 3,
            "parent_id" => 1,
            "name" => "fld",
            "access" => null
        ));

        //folder does not exist
        $I->sendGET('/api/v1/folders/100000');
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

        // get user access type
        $I->sendGET('/api/v1/folders/6/users/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "user_id" => 2,
            "folder_id" => 6,
            "access" => 2
        ));

        $I->sendGET('/api/v1/folders/1/users/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "user_id" => 1,
            "folder_id" => 1,
            "access" => 1
        ));

        // access type not found
        $I->sendGET('/api/v1/folders/1/users/3');
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
            'detail' => "Diritti non trovati sulla cartella 1 per l’utente 3",
            'status' => 404
        ));

        $I->sendGET('/api/v1/folders/1/users/100000');
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

        // GET passwords in specified folder
        $I->sendGET('/api/v1/folders/1/passwords');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/folders/2/passwords');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/folders/3/passwords');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/folders/100000/passwords');
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

        // GET users in specified folder
        $I->sendGET('/api/v1/folders/1/users');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "users" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/folders/2/users');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "users" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/folders/3/users');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "users" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/folders/100000/users');
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

        // create a folder
        $I->sendPOST('/api/v1/folders', [
            'name' => 'testFoldy',
            'parent_id' => null
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "name" => "testFoldy",
            "parent_id" => null
        ));
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->fldIdToElim = $resp['folder_id'];

        // bad body info
        $I->sendPOST('/api/v1/passwords', []);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST); // 400
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'errors' => 'array'
        ]);
        $I->seeResponseContainsJson(array(
            'title' => 'Bad Request',
            'status' => 400
        ));

        // Add access to this newly created folder
        $I->sendPOST('/api/v1/folders/' . $this->fldIdToElim . '/users/2', [
            "access" => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "user_id" => 2,
            "folder_id" => $this->fldIdToElim,
            "access" => 2
        ));

        // sending it again -> 422 already set!
        $I->sendPOST('/api/v1/folders/' . $this->fldIdToElim . '/users/2', [
            "access" => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNPROCESSABLE_ENTITY); //422
        $I->seeResponseContainsJson(array(
            'title' => 'Entità non processabile',
            'status' => 422
        ));

        //update access on this folder
        $I->sendPATCH('/api/v1/folders/' . $this->fldIdToElim . '/users/2', [
            "access" => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "user_id" => 2,
            "folder_id" => $this->fldIdToElim,
            "access" => 1
        ));

        // removing access
        $I->sendDELETE('/api/v1/folders/' . $this->fldIdToElim . '/users/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204

        // sending it again
        $I->sendDELETE('/api/v1/folders/' . $this->fldIdToElim . '/users/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNPROCESSABLE_ENTITY); //422
        $I->seeResponseContainsJson(array(
            'title' => 'Entità non processabile',
            'status' => 422
        ));

        // patch this folder
        $I->sendPATCH('/api/v1/folders/' . $this->fldIdToElim, [
            "name" => "thisFoldyUpdated"
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "folder_id" => $this->fldIdToElim,
            "name" => "thisFoldyUpdated"
        ));

        //delete this folder
        $I->sendDELETE('/api/v1/folders/' . $this->fldIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204

        $I->sendDELETE('/api/v1/folders/' . $this->fldIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); // 404
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

        /*
         *
         *
         *
         *
         *
         *
         *
         */
        // ---------USER-------------
        $I->amBearerAuthenticated($this->userToken);

        // GET FOLDERS
        $I->sendGET('/api/v1/folders');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "folders" => "array"
            ]
        ]);

        // GET SPECIFIC FOLDER
        // read
        $I->sendGET('/api/v1/folders/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "folder_id" => 2,
            "parent_id" => 1,
            "name" => "fld2",
            "access" => 1
        ));

        // manage
        $I->sendGET('/api/v1/folders/3');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "folder_id" => 3,
            "parent_id" => 1,
            "name" => "fld",
            "access" => 2
        ));

        // null
        $I->sendGET('/api/v1/folders/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        //folder does not exist
        $I->sendGET('/api/v1/folders/100000');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'title' => 'string',
            'type' => 'string',
            'status' => 'integer',
            'detail' => 'string'
        ]);
        $I->seeResponseContainsJson(array(
            'title' => 'Unauthorized',
            'status' => 401
        ));

        // get user access type
        $I->sendGET('/api/v1/folders/6/users/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "user_id" => 2,
            "folder_id" => 6,
            "access" => 2
        ));

        $I->sendGET('/api/v1/folders/1/users/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // access type not found
        $I->sendGET('/api/v1/folders/1/users/3');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();

        $I->sendGET('/api/v1/folders/1/users/100000');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();

        // GET passwords in specified folder
        //null
        $I->sendGET('/api/v1/folders/1/passwords');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // read
        $I->sendGET('/api/v1/folders/2/passwords');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        // manage
        $I->sendGET('/api/v1/folders/3/passwords');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/folders/100000/passwords');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();

        // GET users in specified folder
        // null
        $I->sendGET('/api/v1/folders/1/users');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // read
        $I->sendGET('/api/v1/folders/2/users');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "users" => "array"
            ]
        ]);

        // manage
        $I->sendGET('/api/v1/folders/3/users');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "users" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/folders/100000/users');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401

        // create a folder where user has manage
        $I->sendPOST('/api/v1/folders', [
            'name' => 'testFoldy',
            'parent_id' => 3
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "name" => "testFoldy",
            "parent_id" => 3
        ));
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->fldIdToElim = $resp['folder_id'];

        //create folder where user has read
        $I->sendPOST('/api/v1/folders', [
            'name' => 'testFoldy',
            'parent_id' => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();

        //create folder where user has null
        $I->sendPOST('/api/v1/folders', [
            'name' => 'testFoldy',
            'parent_id' => null
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();

        // Add access to this newly created folder
        $I->sendPOST('/api/v1/folders/' . $this->fldIdToElim . '/users/3', [
            "access" => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "user_id" => 3,
            "folder_id" => $this->fldIdToElim,
            "access" => 2
        ));

        // add access to a folder user 2 has NOT manage on
        $I->sendPOST('/api/v1/folders/2/users/3', [
            "access" => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 200
        $I->seeResponseIsJson();

        // sending it again -> 422 already set!
        $I->sendPOST('/api/v1/folders/' . $this->fldIdToElim . '/users/3', [
            "access" => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNPROCESSABLE_ENTITY); //422
        $I->seeResponseContainsJson(array(
            'title' => 'Entità non processabile',
            'status' => 422
        ));

        //update access on this folder
        $I->sendPATCH('/api/v1/folders/' . $this->fldIdToElim . '/users/3', [
            "access" => 1
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "user_id" => 3,
            "folder_id" => $this->fldIdToElim,
            "access" => 1
        ));

        // update access to a folder user 2 has NOT manage on
        $I->sendPATCH('/api/v1/folders/2/users/3', [
            "access" => 2
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();

        // removing access
        $I->sendDELETE('/api/v1/folders/' . $this->fldIdToElim . '/users/3');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204

        // sending it again
        $I->sendDELETE('/api/v1/folders/' . $this->fldIdToElim . '/users/3');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNPROCESSABLE_ENTITY); //422
        $I->seeResponseContainsJson(array(
            'title' => 'Entità non processabile',
            'status' => 422
        ));

        // remove access to a folder user 2 has NOT manage on
        $I->sendDELETE('/api/v1/folders/2/users/3');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();

        // patch this folder
        $I->sendPATCH('/api/v1/folders/' . $this->fldIdToElim, [
            "name" => "thisFoldyUpdated"
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "folder_id" => $this->fldIdToElim,
            "name" => "thisFoldyUpdated"
        ));

        // patch another folder where user has NOT manage on
        $I->sendPATCH('/api/v1/folders/1', [
            "name" => "thisFoldyUpdated"
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401

        //delete this folder
        $I->sendDELETE('/api/v1/folders/' . $this->fldIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT); // 204

        //delete another folder where user has NOT manage on it
        $I->sendDELETE('/api/v1/folders/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 204

        $I->sendDELETE('/api/v1/folders/' . $this->fldIdToElim);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); // 404
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

        // we can test user3 can create under 'root'
        // $I->amBearerAuthenticated($this->user2Token);
        // this is stupid, who has create_folders=1 can create folders, but not delete/update them?
    }
}
