<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

// configurazione globale per crypt / decrypt delle password
return [
    // PasswordFacadeFactory creates a blockCipher
    "block_cipher" => [
        // password
        "key" => "",
        'encryption_library' => 'openssl',
        'algorithms' => [
            'algo' => 'aes',
            'mode' => 'gcm'
        ]
    ]
];
