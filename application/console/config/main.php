<?php

use Sil\JsonLog\target\JsonSyslogTarget;

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => JsonSyslogTarget::class,
                    'categories' => ['application'], // stick to messages from this app, not all of Yii's built-in messaging.
                    'logVars' => [], // no need for default stuff: http://www.yiiframework.com/doc-2.0/yii-log-target.html#$logVars-detail
                ],
            ]
        ],
    ]
];
