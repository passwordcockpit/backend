<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

class LogsTestCest
{
    private $userToken;
    private $adminToken;
    private $user2Token;

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
        $I->sendPOST('/api/auth', [
            'username' => 'admin',
            'password' => 'Admin123!'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();
        $this->adminToken = $resp['token'];

        // get user2 token
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
        // test log get with admin
         */
        $I->amBearerAuthenticated($this->adminToken);
        $I->sendGET('/api/v1/logs/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "log_id" => "integer",
            "password_id" => "integer",
            "user_id" => "integer",
            "username" => "string",
            "action_date" => "string",
            "action" => "string"
        ]);

        // trying to get a log that does not exist
        $I->sendGET('/api/v1/logs/100000');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND); // 404
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('title' => 'Risorsa non trovata'));
        $I->seeResponseContainsJson(array('status' => '404'));

        /*
        // test log get with user (no view_logs permission)
         */
        $I->amBearerAuthenticated($this->userToken);
        $I->sendGET('/api/v1/logs/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('title' => 'Unauthorized'));
        $I->seeResponseContainsJson(array('status' => '401'));

        $I->sendGET('/api/v1/logs/100000');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('title' => 'Unauthorized'));
        $I->seeResponseContainsJson(array('status' => '401'));

        /*
        // LOGIN WITH USER2
         */

        //specifics logs
        $I->amBearerAuthenticated($this->user2Token);
        $I->sendGET('/api/v1/logs/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "log_id" => "integer",
            "password_id" => "integer",
            "user_id" => "integer",
            "username" => "string",
            "action_date" => "string",
            "action" => "string"
        ]);

        $I->sendGET('/api/v1/logs/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('title' => 'Unauthorized'));
        $I->seeResponseContainsJson(array('status' => '401'));

        //specific passwords
        $I->sendGET('/api/v1/passwords/4/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "logs" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/passwords/1/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('title' => 'Unauthorized'));
        $I->seeResponseContainsJson(array('status' => '401'));

        $I->sendGET('/api/v1/passwords/100000/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('title' => 'Unauthorized'));
        $I->seeResponseContainsJson(array('status' => '401'));

        //specific user
        $I->sendGET('/api/v1/users/3/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "logs" => "array"
            ]
        ]);

        $I->sendGET('/api/v1/users/1/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('title' => 'Unauthorized'));
        $I->seeResponseContainsJson(array('status' => '401'));

        $I->sendGET('/api/v1/users/100000/logs');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('title' => 'Unauthorized'));
        $I->seeResponseContainsJson(array('status' => '401'));
    }
}
