<?php

use common\components\Mailer;
use Sil\JsonLog\target\JsonStreamTarget;
use Sil\Log\EmailTarget;
use Sil\PhpEnv\Env;
use yii\db\Connection;
use yii\helpers\Json;

$appName       = Env::requireEnv('APP_NAME');
$fromEmail     = Env::requireEnv('FROM_EMAIL');
$mysqlHost     = Env::requireEnv('MYSQL_HOST');
$mysqlDatabase = Env::requireEnv('MYSQL_DATABASE');
$mysqlUser     = Env::requireEnv('MYSQL_USER');
$mysqlPassword = Env::requireEnv('MYSQL_PASSWORD');
$notificationEmail = Env::requireEnv('NOTIFICATION_EMAIL');

$emailQueueBatchSize = Env::get('EMAIL_QUEUE_BATCH_SIZE', 10);
$mailerUseFiles      = Env::get('MAILER_USEFILES', false);

$logPrefix = function () {
    $request = Yii::$app->request;
    $prefixData = [
        'env' => YII_ENV,
    ];
    if ($request instanceof \yii\web\Request) {
        // Assumes format: Bearer consumer-module-name-32randomcharacters
        $prefixData['id'] = substr($request->headers['Authorization'], 7, 16) ?: 'unknown';
        $prefixData['ip'] = $request->getUserIP();
        $prefixData['method'] = $request->getMethod();
        $prefixData['url'] = $request->getUrl();
    } elseif ($request instanceof \yii\console\Request) {
        $prefixData['id'] = '(console)';
    }

    return Json::encode($prefixData);
};


return [
    'id' => 'app-common',
    'bootstrap' => ['log'],
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => "mysql:host=$mysqlHost;dbname=$mysqlDatabase",
            'username' => $mysqlUser,
            'password' => $mysqlPassword,
            'charset' => 'utf8',
        ],
        // http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
        'log' => [
            'targets' => [
                [
                    'class' => JsonStreamTarget::class,
                    'url' => 'php://stdout',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => ['application'],
                    'prefix' => $logPrefix,
                    'exportInterval' => 1,
                ],
                [
                    'class' => JsonStreamTarget::class,
                    'url' => 'php://stderr',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                    'prefix' => $logPrefix,
                    'exportInterval' => 1,
                ],
                [
                    'class' => EmailTarget::class,
                    'categories' => ['application'], // stick to messages from this app, not all of Yii's built-in messaging.
                    'logVars' => [], // no need for default stuff: http://www.yiiframework.com/doc-2.0/yii-log-target.html#$logVars-detail
                    'levels' => ['error'],
                    'message' => [
                        'from' => $fromEmail,
                        'to' => $notificationEmail,
                        'subject' => "ERROR - $appName [".YII_ENV."] Error",
                    ],
                    'exportInterval' => 1,
                ],
            ],
        ],
        'mailer' => [
            'class' => Mailer::class,
            'useFileTransport' => $mailerUseFiles,
            'htmlLayout' => '@common/mail/layouts/html',
            'textLayout' => '@common/mail/layouts/text',
        ],
    ],
    'params' => [
        'fromEmail' => $fromEmail,
        'emailQueueBatchSize' => $emailQueueBatchSize,
    ],
];
