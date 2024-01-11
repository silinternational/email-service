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
        Email::sendQueuedEmail();
    }
}
