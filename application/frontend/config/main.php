<?php

use common\models\ApiConsumer;
use Sil\JsonLog\target\JsonSyslogTarget;
use Sil\PhpEnv\Env;
use yii\helpers\Json;
use yii\web\JsonParser;
use yii\web\Response;

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    // http://www.yiiframework.com/doc-2.0/guide-structure-applications.html#controllerNamespace
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => JsonSyslogTarget::class,
                    'categories' => ['application'], // stick to messages from this app, not all of Yii's built-in messaging.
                    'logVars' => [], // no need for default stuff: http://www.yiiframework.com/doc-2.0/yii-log-target.html#$logVars-detail
                    'prefix' => function () {
                        /* @var Request */
                        $request = Yii::$app->request;

                        // Assumes format: Bearer consumer-module-name-32randomcharacters
                        $requesterId = substr($request->headers['Authorization'], 7, 16) ?: 'unknown';

                        $prefixData = [
                            'env' => YII_ENV,
                            'id' => $requesterId,
                            'ip' => $request->getUserIP(),
                        ];

                        return Json::encode($prefixData);
                    },
                ],
            ]
        ],
        // http://www.yiiframework.com/doc-2.0/guide-security-authentication.html
        'user' => [
            'identityClass' => ApiConsumer::class, // custom Bearer <token> implementation
            'enableSession' => false, // ensure statelessness
        ],
        // http://www.yiiframework.com/doc-2.0/guide-runtime-requests.html
        'request' => [
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
            'enablePrettyUrl' => true, // turns /index.php?r=post%2Fview&id=100 into /index.php/post/100
            'showScriptName' => false, // turns /index.php/post/100 into /post/100
            // http://www.yiiframework.com/doc-2.0/guide-rest-routing.html
            'rules' => [
                'GET  user'                           => 'user/index',
                'GET  user/expiring'                  => 'user/expiring',
                'GET  user/first-password'            => 'user/first-password',
                'GET  user/<employeeId:\w+>'          => 'user/view',
                'POST user'                           => 'user/create',
                'PUT  user/<employeeId:\w+>'          => 'user/update',
                'PUT  user/<employeeId:\w+>/password' => 'user/update-password',

                'POST authentication' => 'authentication/create',

                'site/status' => 'site/status',

                '<undefinedRequest>' => 'site/undefined-request',
            ]
        ],
    ],
    'params' => [
        'authorizedTokens'              => Env::getArray('API_ACCESS_KEYS'),
        'passwordReuseLimit'            => Env::get('PASSWORD_REUSE_LIMIT', 10),
        'passwordLifespan'              => Env::get('PASSWORD_LIFESPAN', '+1 year'),
        'passwordExpirationGracePeriod' => Env::get('PASSWORD_EXPIRATION_GRACE_PERIOD', '+30 days'),
    ],
];
