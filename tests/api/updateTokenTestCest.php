<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

class updateTokenTestCest
{
    private $token;

    public function _before(ApiTester $I)
    {
        // get the token
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/auth', [
            'username' => 'user',
            'password' => 'user'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();

        $resp = new Codeception\Util\JsonArray($I->grabResponse());
        $resp = $resp->toArray();

        $this->token = $resp['token'];
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
        // test update token
        $I->amBearerAuthenticated($this->token);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/v1/token/update');

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'token' => 'string'
        ]);

        //using a wrong token
        $I->amBearerAuthenticated(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDI5MDIyOTYsImV4cCI6MTU0MjkzODI5NiwiZGF0YSI6eyJsYW5ndWFnZSI6Iml0IiwibGRhcCI6ZmFsc2V9LCJzdWIiOjF9.n33gclNRWcIWQza0nvP6Gb3X-Jynny5jetGzxZGhrhY'
        );
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/v1/token/update');

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200 -> default by slim/jwt...
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('detail' => 'Expired token'));
    }
}
