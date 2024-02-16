<?php

namespace frontend\controllers;

use Exception;
use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;

class SiteController extends Controller
{
    public const HttpExceptionBadGateway = 502;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'] = [
            // bypass authentication, i.e., public API
            'status'
        ];

        return $behaviors;
    }

    public function actionStatus()
    {
        try {
            // db comms are a good indication of health
            Yii::$app->db->open();
        } catch (Exception $e) {
            \Yii::error($e->getMessage());
            throw new HttpException(
                self::HttpExceptionBadGateway,
                'Database connection problem.',
                $e->getCode()
            );
        }

        Yii::$app->response->statusCode = 204;
    }

    public function actionUndefinedRequest()
    {
        $method = Yii::$app->request->method;
        $url    = Yii::$app->request->url;

        Yii::warning("$method $url requested but not defined.");

        throw new NotFoundHttpException();
    }
}
