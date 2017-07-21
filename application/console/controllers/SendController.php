<?php
namespace console\controllers;

use common\models\Email;
use yii\console\Controller;

class SendController extends Controller
{
    /**
     * Retry sending emails from queue in small batch sizes
     */
    public function actionSendQueuedEmail()
    {
        try {
            echo 'starting cron/send-queued-email' . PHP_EOL;

            $batchSize = \Yii::$app->params['emailQueueBatchSize'];
            $queued = Email::find()->orderBy(['updated_at' => SORT_ASC])->limit($batchSize)->all();

            if (empty($queued)) {
                echo 'no queued emails to send' . PHP_EOL;
                return;
            }

            echo 'starting to process ' . count($queued) . ' queued emails...' . PHP_EOL;

            /** @var Email $email */
            foreach ($queued as $email) {
                $email->retry();
            }
        } catch (\Exception $e) {
            echo 'error occurred' . PHP_EOL;
            \Yii::error([
                'action' => 'send/sendQueuedEmail',
                'status' => 'error',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }

        echo 'done' . PHP_EOL;
    }
}