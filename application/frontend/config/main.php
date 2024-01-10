<?php

use common\models\ApiConsumer;
use Sil\PhpEnv\Env;
use yii\web\JsonParser;
use yii\web\Response;

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    // http://www.yiiframework.com/doc-2.0/guide-structure-applications.html#controllerNamespace
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        // http://www.yiiframework.com/doc-2.0/guide-security-authentication.html
        'user' => [
            'identityClass' => ApiConsumer::class, // custom Bearer <token> implementation
            'enableSession' => false, // ensure statelessness
        ],
        // http://www.yiiframework.com/doc-2.0/guide-runtime-requests.html
        'request' => [
            'enableCsrfCookie' => false,

            // restrict input to JSON only http://www.yiiframework.com/doc-2.0/guide-rest-quick-start.html#enabling-json-input
            'parsers' => [
                'application/json' => JsonParser::class,
            ]
        ],
        // http://www.yiiframework.com/doc-2.0/guide-runtime-responses.html
        'response' => [
            // all responses, even unhandled errors, need to be in JSON for an API.
            'format' => Response::FORMAT_JSON,
        ],
        // http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html
        'urlManager' => [
            'cache' => null,
            'enablePrettyUrl' => true, // turns /index.php?r=post%2Fview&id=100 into /index.php/post/100
            'showScriptName' => false, // turns /index.php/post/100 into /post/100
            // http://www.yiiframework.com/doc-2.0/guide-rest-routing.html
            'rules' => [
                'POST email' => 'email/queue',

                'site/status' => 'site/status',

                '<undefinedRequest>' => 'site/undefined-request',
            ]
        ],
    ],
    'params' => [
        'authorizedTokens' => Env::getArray('API_ACCESS_KEYS'),
    ],
];
