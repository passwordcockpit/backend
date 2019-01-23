<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

class UsersTestCest
{
    private $userToken;
    private $adminToken;
    private $user2Token;

    public function _createUser()
    {
        $randCode = '';
        for ($i = 0; $i < 8; $i++) {
            $randCode = $randCode . rand(0, 9);
        }
        $username = "CC-" . $randCode;
        $password = "User123!";
        $name = "CCTest";
        $surname = "deletedis";
        $language = "it";
        $phone = "0911502727";
        $email = $randCode . "@blackpoints.ch";
        $enabled = true;

        $user = [
            'username' => $username,
            'password' => $password,
            'name' => $name,
            'surname' => $surname,
            'language' => $language,
            'phone' => $phone,
            'email' => $email,
            'enabled' => $enabled
        ];

        return $user;
    }

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

        // get the user2
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/auth', [
            'username' => 'user2',
            'password' => 'User123!'
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
         *
         */
        // ADMIN
        /*
         *
         *
         */
        $I->amBearerAuthenticated($this->adminToken);

        // list users
        $I->sendGET('/api/v1/users');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "users" => "array"
            ]
        ]);

        // list users half
        $I->sendGET('/api/v1/users/usernames');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "users" => "array"
            ]
        ]);

        // list specific user (himself)
        $I->sendGET('/api/v1/users/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "user_id" => "integer",
            "username" => "string",
            "name" => "string",
            "surname" => "string",
            "enabled" => "boolean",
            "phone" => "string",
            "email" => "string",
            "language" => "string"
        ]);

        // list specific user (user id=2)
        $I->sendGET('/api/v1/users/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "user_id" => "integer",
            "username" => "string",
            "name" => "string",
            "surname" => "string",
            "enabled" => "boolean",
            "phone" => "string",
            "email" => "string",
            "language" => "string"
        ]);

        // list logs of himself
        $I->sendGET('/api/v1/users/1/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "logs" => "array"
            ]
        ]);

        // list logs of user id=2
        $I->sendGET('/api/v1/users/2/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "logs" => "array"
            ]
        ]);

        // test create user
        $I->sendPOST('/api/v1/users', $this->_createUser());
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "user_id" => "integer",
            "username" => "string",
            "name" => "string",
            "surname" => "string",
            "enabled" => "boolean",
            "phone" => "string",
            "email" => "string",
            "language" => "string"
        ]);

        //wrong create user
        $I->sendPOST('/api/v1/users', [
            "username" => "string",
            "password" => "string",
            "name" => "string",
            "surname" => "string",
            "language" => "string",
            "phone" => "string",
            "email" => "string",
            "enabled" => true
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST); // 400
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'errors' => 'array'
        ]);
        $I->seeResponseContainsJson(array(
            'status' => 400,
            'detail' => 'Errore di validazione'
        ));

        // same username -> error
        $I->sendPOST('/api/v1/users', [
            "username" => "user",
            "password" => "string",
            "name" => "string",
            "surname" => "string",
            "language" => "it",
            "phone" => "019276214",
            "email" => "string@tring.com",
            "enabled" => true
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST); // 400
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'errors' => 'array'
        ]);
        $I->seeResponseContainsJson(array(
            'status' => 400,
            'detail' => 'Errore di validazione'
        ));

        // update existing user (user id=2)
        $I->sendPATCH('/api/v1/users/2', [
            "phone" => "0915546234",
            "surname" => "user"
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            'user_id' => 2,
            'username' => 'user',
            'name' => 'User',
            'surname' => 'user',
            'enabled' => true,
            'phone' => '0915546234',
            'language' => 'it'
        ));

        // update himself
        $I->sendPATCH('/api/v1/users/1', [
            "phone" => "0915546234",
            "email" => "changed1@blackpoints.ch"
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            'user_id' => 1,
            'username' => 'admin',
            'name' => 'Admin',
            'surname' => 'Admin',
            'enabled' => true,
            'phone' => '0915546234',
            'email' => 'changed1@blackpoints.ch',
            'language' => 'it'
        ));

        // check user id=2 permission
        $I->sendGET('/api/v1/users/2/permissions');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            'user_id' => 2,
            'manage_users' => false,
            'create_folders' => false,
            'access_all_folders' => false,
            'view_logs' => false
        ));

        // check permissions of himself
        $I->sendGET('/api/v1/users/1/permissions');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            'user_id' => 1,
            'manage_users' => true,
            'create_folders' => true,
            'access_all_folders' => true,
            'view_logs' => true
        ));

        // update permission of existing user (user id=2)
        $I->sendPATCH('/api/v1/users/2/permissions', [
            "manage_users" => true,
            "create_folders" => true,
            "access_all_folders" => true,
            "view_logs" => true
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            'user_id' => 2,
            'manage_users' => true,
            'create_folders' => true,
            'access_all_folders' => true,
            'view_logs' => true
        ));

        // update permission of himself
        $I->sendPATCH('/api/v1/users/1/permissions', [
            "manage_users" => true,
            "create_folders" => false,
            "access_all_folders" => true,
            "view_logs" => false
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            'user_id' => 1,
            'manage_users' => true,
            'create_folders' => false,
            'access_all_folders' => true,
            'view_logs' => false
        ));

        //revert the permission changes
        $I->sendPATCH('/api/v1/users/1/permissions', [
            "manage_users" => true,
            "create_folders" => true,
            "access_all_folders" => true,
            "view_logs" => true
        ]);
        $I->sendPATCH('/api/v1/users/2/permissions', [
            "manage_users" => false,
            "create_folders" => false,
            "access_all_folders" => false,
            "view_logs" => false
        ]);

        /*
         *
         *
         */
        // SWITCH TO USER (NO PERMISSIONS)
        /*
         *
         *
         */
        $I->amBearerAuthenticated($this->userToken);

        // list users
        $I->sendGET('/api/v1/users');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // list users half
        $I->sendGET('/api/v1/users/usernames');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "users" => "array"
            ]
        ]);

        // list specific user (NOT himself)
        $I->sendGET('/api/v1/users/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // list specific user (himself)
        $I->sendGET('/api/v1/users/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            'user_id' => 2,
            'username' => 'user',
            'name' => 'User',
            'surname' => 'user',
            'enabled' => true,
            'phone' => '0915546234',
            'email' => 'changed@blackpoints.ch',
            'language' => 'it'
        ));

        // list logs of himself
        $I->sendGET('/api/v1/users/2/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // list logs of another user
        $I->sendGET('/api/v1/users/1/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // test create user
        $I->sendPOST('/api/v1/users', $this->_createUser());
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare POST su questa risorsa"
        ));

        // test create user with wrong data
        $I->sendPOST('/api/v1/users', [
            "username" => "string",
            "password" => "string",
            "name" => "string",
            "surname" => "string",
            "language" => "string",
            "phone" => "string",
            "email" => "string",
            "enabled" => true
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare POST su questa risorsa"
        ));

        // update himself
        $I->sendPATCH('/api/v1/users/2', [
            "phone" => "0915546234"
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            'user_id' => 2,
            'username' => 'user',
            'name' => 'User',
            'surname' => 'user',
            'enabled' => true,
            'phone' => '0915546234',
            'language' => 'it'
        ));

        // update another user
        $I->sendPATCH('/api/v1/users/1', [
            "phone" => "0915546234",
            "email" => "changed1@blackpoints.ch"
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare PATCH su questa risorsa"
        ));

        // check himself's permissions
        $I->sendGET('/api/v1/users/2/permissions');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            'user_id' => 2,
            'manage_users' => false,
            'create_folders' => false,
            'access_all_folders' => false,
            'view_logs' => false
        ));

        // check permissions of another user
        $I->sendGET('/api/v1/users/1/permissions');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare GET su questa risorsa"
        ));

        // update permissions of himself
        $I->sendPATCH('/api/v1/users/2/permissions', [
            "manage_users" => true,
            "create_folders" => true,
            "access_all_folders" => true,
            "view_logs" => true
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare PATCH su questa risorsa"
        ));

        // update permissions of another user
        $I->sendPATCH('/api/v1/users/1/permissions', [
            "manage_users" => true,
            "create_folders" => true,
            "access_all_folders" => true,
            "view_logs" => true
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user non può effettuare PATCH su questa risorsa"
        ));
        /*
         *
         *
         */
        // SWITCH TO USER2 (NO PERMISSIONS, NO 'MANAGE' ON ANY FOLDER)
        /*
         *
         *
         */
        $I->amBearerAuthenticated($this->user2Token);

        $I->sendGET('/api/v1/users/usernames');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array(
            "title" => "Unauthorized",
            "type" => "https://httpstatuses.com/401",
            "status" => 401,
            "detail" => "Utente user2 non può effettuare GET su questa risorsa"
        ));
    }
}
