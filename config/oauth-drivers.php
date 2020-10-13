<?php

return [
    'gitlab' => [
        'urlAuthorize' => 'oauth/authorize',
        'urlAccessToken' => 'oauth/token',
        'urlResourceOwnerDetails' => 'api/v4/user'
    ],
    'bitbucket' => [
        'urlAuthorize' => '1.0/oauth/authenticate',
        'urlAccessToken' => '1.0/oauth/access_token',
        'urlResourceOwnerDetails' => '2.0/user'
    ]
];
