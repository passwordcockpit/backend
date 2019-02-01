<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

return [
    'rbac' => [
        'roles' => [
            'user' => [],
            'admin' => [],
            'manage_users' => [],
            'create_folders' => [],
            'access_all_folders' => [],
            'view_logs' => []
        ],
        'permissions' => [
            'manage_users' => [
                'api.v1.users.get',
                'api.v1.users.list',
                'api.v1.usernames.list',
                'api.v1.users.create',
                'api.v1.users.update',
                'api.v1.users.permissions.get',
                'api.v1.users.permissions.update'
            ],
            'create_folders' => ['api.v1.folders.create'],
            'view_logs' => [
                'api.v1.logs.get',
                'api.v1.passwords.logs.list',
                'api.v1.users.logs.list'
            ],
            'access_all_folders' => [
                'api.v1.folders.list',
                'api.v1.folders.delete',
                'api.v1.folders.get',
                'api.v1.users.list',
                'api.v1.usernames.list',
                'api.v1.folders.create',
                'api.v1.folders.update',
                'api.v1.folders.users.list',
                'api.v1.folders.passwords.list',
                'api.v1.folders.users.add',
                'api.v1.folders.users.update',
                'api.v1.folders.users.delete',
                'api.v1.folders.users.get',
                'api.v1.passwords.list',
                'api.v1.passwords.get',
                'api.v1.passwords.create',
                'api.v1.passwords.update',
                'api.v1.passwords.delete',
                'api.v1.files.get',
                'api.v1.upload.files.get',
                'api.v1.files.list',
                'api.v1.files.update',
                'api.v1.files.delete',
                'api.v1.passwords.files.list'
            ],
            'user' => [
                'api.v1.users.update',
                'api.v1.users.get',
                'api.v1.users.permissions.get',
                'api.v1.usernames.list',
                'api.v1.folders.list',
                'api.v1.folders.delete',
                'api.v1.search.password',
                'api.v1.search.folders',
                'api.v1.folders.get',
                'api.v1.folders.create',
                'api.v1.folders.update',
                'api.v1.folders.users.list',
                'api.v1.folders.passwords.list',
                'api.v1.folders.users.add',
                'api.v1.folders.users.update',
                'api.v1.folders.users.delete',
                'api.v1.folders.users.get',
                'api.v1.passwords.list',
                'api.v1.passwords.get',
                'api.v1.passwords.create',
                'api.v1.passwords.update',
                'api.v1.passwords.delete',
                'api.v1.files.get',
                'api.v1.upload.files.get',
                'api.v1.authentication.logout',
                'api.v1.files.list',
                'api.v1.authorization.logout',
                'api.v1.authorization.update',
                'api.v1.files.update',
                'api.v1.files.delete',
                'api.v1.passwords.files.list'
            ]
        ],
        'assertions' => [
            'factories' => [
                Authorization\Api\V1\Assertion\FileAssertion::class =>
                    Authorization\Api\V1\Factory\Assertion\FileAssertionFactory::class,
                Authorization\Api\V1\Assertion\UserAssertion::class =>
                    Authorization\Api\V1\Factory\Assertion\UserAssertionFactory::class,
                Authorization\Api\V1\Assertion\PasswordAssertion::class =>
                    Authorization\Api\V1\Factory\Assertion\PasswordAssertionFactory::class,
                Authorization\Api\V1\Assertion\FolderAssertion::class =>
                    Authorization\Api\V1\Factory\Assertion\FolderAssertionFactory::class,
                Authorization\Api\V1\Assertion\ManageFolderAssertion::class =>
                    Authorization\Api\V1\Factory\Assertion\ManageFolderAssertionFactory::class,
                Authorization\Api\V1\Assertion\ManagePasswordAssertion::class =>
                    Authorization\Api\V1\Factory\Assertion\ManagePasswordAssertionFactory::class,
                Authorization\Api\V1\Assertion\UsernameListAssertion::class =>
                    Authorization\Api\V1\Factory\Assertion\UsernameListAssertionFactory::class,
                Authorization\Api\V1\Assertion\LogsAssertion::class =>
                    Authorization\Api\V1\Factory\Assertion\LogsAssertionFactory::class,
                Authorization\Api\V1\Assertion\FileManageAssertion::class =>
                    Authorization\Api\V1\Factory\Assertion\FileManageAssertionFactory::class
            ],
            'registeredByRoute' => [
                'api.v1.passwords.logs.list' => [
                    Authorization\Api\V1\Assertion\LogsAssertion::class
                ],
                'api.v1.logs.get' => [
                    Authorization\Api\V1\Assertion\LogsAssertion::class
                ],
                'api.v1.users.logs.list' => [
                    Authorization\Api\V1\Assertion\LogsAssertion::class
                ],
                'api.v1.folders.delete' => [
                    Authorization\Api\V1\Assertion\ManageFolderAssertion::class
                ],
                'api.v1.folders.get' => [
                    Authorization\Api\V1\Assertion\FolderAssertion::class
                ],
                'api.v1.folders.create' => [
                    Authorization\Api\V1\Assertion\ManageFolderAssertion::class
                ],
                'api.v1.folders.update' => [
                    Authorization\Api\V1\Assertion\ManageFolderAssertion::class
                ],
                'api.v1.folders.users.list' => [
                    Authorization\Api\V1\Assertion\FolderAssertion::class
                ],
                'api.v1.folders.passwords.list' => [
                    Authorization\Api\V1\Assertion\FolderAssertion::class
                ],
                'api.v1.folders.users.add' => [
                    Authorization\Api\V1\Assertion\ManageFolderAssertion::class
                ],
                'api.v1.folders.users.update' => [
                    Authorization\Api\V1\Assertion\ManageFolderAssertion::class
                ],
                'api.v1.folders.users.delete' => [
                    Authorization\Api\V1\Assertion\ManageFolderAssertion::class
                ],
                'api.v1.folders.users.get' => [
                    Authorization\Api\V1\Assertion\FolderAssertion::class
                ],
                'api.v1.passwords.get' => [
                    Authorization\Api\V1\Assertion\PasswordAssertion::class
                ],
                'api.v1.passwords.create' => [
                    Authorization\Api\V1\Assertion\ManageFolderAssertion::class
                ],
                'api.v1.passwords.update' => [
                    Authorization\Api\V1\Assertion\ManagePasswordAssertion::class
                ],
                'api.v1.passwords.delete' => [
                    Authorization\Api\V1\Assertion\ManagePasswordAssertion::class
                ],
                'api.v1.passwords.files.list' => [
                    Authorization\Api\V1\Assertion\PasswordAssertion::class
                ],
                'api.v1.users.update' => [
                    Authorization\Api\V1\Assertion\UserAssertion::class
                ],
                'api.v1.users.get' => [
                    Authorization\Api\V1\Assertion\UserAssertion::class
                ],
                'api.v1.users.permissions.get' => [
                    Authorization\Api\V1\Assertion\UserAssertion::class
                ],
                'api.v1.files.get' => [
                    Authorization\Api\V1\Assertion\FileAssertion::class
                ],
                'api.v1.files.delete' => [
                    Authorization\Api\V1\Assertion\FileManageAssertion::class
                ],
                'api.v1.files.update' => [
                    Authorization\Api\V1\Assertion\ManagePasswordAssertion::class
                ],
                'api.v1.upload.files.get' => [
                    Authorization\Api\V1\Assertion\FileAssertion::class
                ],
                'api.v1.files.list' => [
                    Authorization\Api\V1\Assertion\FileAssertion::class
                ],
                'api.v1.usernames.list' => [
                    Authorization\Api\V1\Assertion\UsernameListAssertion::class
                ]
            ]
        ]
    ]
];
