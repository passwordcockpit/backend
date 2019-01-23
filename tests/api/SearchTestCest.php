<?php

/**
 * @see https://github.com/passwordcockpitackend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpitackend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

class SearchTestCest
{
    private $userToken;
    private $adminToken;

    public function _before(ApiTester $I)
    {
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
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
        /**
         *
         * ADMIN
         *
         */
        $I->amBearerAuthenticated($this->adminToken);

        // no parameters should end in 200
        $I->sendGET('/api/v1/passwords?q=');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200

        // no parameters should end in 200
        $I->sendGET('/api/v1/folders?q=');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200

        $I->sendGET('/api/v1/passwords?q=');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);
        //pass 1,2 should show
        $I->seeResponseContainsJson(array(
            "_embedded" => [
                "passwords" => [
                    [
                        "password_id" => 1,
                        "title" => "pass1"
                    ],
                    [
                        "password_id" => 2,
                        "title" => "pass2"
                    ]
                ]
            ]
        ));

        $I->sendGET('api/v1/folders?q=fld');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "folders" => "array"
            ]
        ]);
        //folder 2,3,4 should show
        $I->seeResponseContainsJson(array(
            "_embedded" => [
                "folders" => [
                    [
                        "folder_id" => 3,
                        "name" => "fld",
                        "access" => null
                    ],
                    [
                        "folder_id" => 2,
                        "name" => "fld2",
                        "access" => 2
                    ],
                    [
                        "folder_id" => 4,
                        "name" => "fld4",
                        "access" => null
                    ]
                ]
            ]
        ));

        /**
         *
         * USER
         *
         */
        $I->amBearerAuthenticated($this->userToken);

        //only folder 2 and 3 should show
        $I->sendGET('api/v1/folders?q=fld');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "folders" => "array"
            ]
        ]);
        $I->seeResponseContainsJson(array(
            "_embedded" => [
                "folders" => [
                    [
                        "folder_id" => 3,
                        "name" => "fld",
                        "access" => 2
                    ],
                    [
                        "folder_id" => 2,
                        "name" => "fld2",
                        "access" => 1
                    ]
                ]
            ]
        ));

        $I->sendGET('/api/v1/passwords?q=');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            "_embedded" => [
                "passwords" => "array"
            ]
        ]);
        //pass 2,4,6 should show
        $I->seeResponseContainsJson(array(
            "_embedded" => [
                "passwords" => [
                    [
                        "password_id" => 6,
                        "title" => "pass6"
                    ],
                    [
                        "password_id" => 2,
                        "title" => "pass2"
                    ],
                    [
                        "password_id" => 4,
                        "title" => "pass4"
                    ]
                ]
            ]
        ));
    }
}
