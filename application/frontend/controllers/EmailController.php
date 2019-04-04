<?php
namespace frontend\controllers;

use Yii;
use common\models\Email;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\UnprocessableEntityHttpException;

class EmailController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Use request header-> 'Authorization: Bearer <token>'
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();

        return $behaviors;
    }

    public function actionQueue(): Email
    {
        $email = new Email();
        $email->attributes = Yii::$app->request->getBodyParams();

        if (! $email->save()) {
            $details = current($email->getFirstErrors());

            Yii::error([
                'action' => 'create email',
                'status' => 'error',
                'error' => $details
            ]);

            throw new UnprocessableEntityHttpException(current($email->getFirstErrors()));
        }

        Yii::info([
           'action' => 'email/queue',
           'status' => 'queued',
           'id' => $email->id,
           'toAddress' => $email->to_address ?? '(null)',
           'subject' => $email->subject ?? '(null)',
        ], 'application');

        /*
         * Attempt to send email immediately
         */
        try {
            $email->send();
        } catch (\Exception $e) {
            // ignore for now, message is already queued
        }
        
        return $email;
    }
}
