<?php
namespace frontend\controllers;

use common\models\Email;
use frontend\components\BaseRestController;
use yii\web\ServerErrorHttpException;

class EmailController extends BaseRestController
{

    public function actionQueue(): Email
    {
        $email = new Email();
        $email->attributes = \Yii::$app->request->getBodyParams();

        $this->save($email);

        return $email;
    }

}
