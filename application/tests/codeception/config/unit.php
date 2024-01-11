<?php
/**
 * Application configuration shared by all applications unit tests
 */

use yii\helpers\ArrayHelper;

$mainConfig = require(__DIR__ . '/../../../common/config/main.php');

$unitTestConfig = [
    'id' => 'unit_tests_app',
    'basePath' => dirname(dirname(dirname(__DIR__))),
    'components' => [
        'mailer' => ArrayHelper::merge($mainConfig['components']['mailer'], [
            'useFileTransport' => true,
        ])
    ],
    'params' => [

    ],
];

return ArrayHelper::merge(
    $mainConfig,
    $unitTestConfig
);
