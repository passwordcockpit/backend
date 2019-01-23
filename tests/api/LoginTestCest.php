<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

class LoginTestCest
{
    public function _before(ApiTester $I)
    {
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
        // LOGIN THAT WORKS
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/auth', [
            'username' => 'admin',
            'password' => 'Admin123!'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();

        // WRONG PASSWORD
        $I->sendPOST('/api/auth', [
            'username' => 'admin',
            'password' => 'wrong'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('status' => '401'));

        // WRONG DATA
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/api/auth', [
            'username' => 'admin',
            'wrong' => 'Admin123!'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST); // 400
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('detail' => 'Validation error'));
    }
}
