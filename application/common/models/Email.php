<?php

namespace common\models;

use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

class Email extends EmailBase
{
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIO_DEFAULT => [
                'to_address',
                'cc_address',
                'bcc_address',
                'subject',
                'text_body',
                'html_body',
                '!created_at', //TODO: might remove after using timestampbehavior
            ],
        ];

        return $scenarios;
    }

    public function rules()
    {
        return ArrayHelper::merge(
            [
                [
                    'attempts_count', 'default', 'value' => 0,
                ],
//TODO: look at http://www.yiiframework.com/doc-2.0/yii-behaviors-timestampbehavior.html
                [
                    'created_at', 'default', 'value' => time(),
                ],
                [
                    ['to_address', 'cc_address', 'bcc_address'], 'email',
                ],
            ],
            parent::rules()
        );

//TODO: add rule to require some content (text or html and non-empty)
    }

    /**
     * Attempt to send email. Returns true on success or throws exception.
     * DOES NOT QUEUE ON FAILURE
     * @return void
     * @throws \Exception
     */
    public function send()
    {
        $log = [
            'action' => 'send email',
            'to' => $this->to_address,
            'subject' => $this->subject,
        ];

        /*
         * Try to send email or throw exception
         */
        try {
            $message = $this->getMessage();
            if ( ! $message->send()) {
                throw new \Exception('Unable to send email', 1461011826);
            }

            /*
             * Remove entry from queue (if saved to queue) after successful send
             */
            $this->removeFromQueue();

            /*
             * Log success
             */
            $log['status'] = 'sent';
            \Yii::info($log, 'application');

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Attempt to send email and on failure update attempts count and save (queue it)
     * @throws \Exception
     */
    public function retry()
    {
        try {
            $this->send();
        } catch (\Exception $e) {
            /*
             * Send failed, attempt to queue
             */
            $this->attempts_count += 1;
            $this->updated_at = time();

            $log = [
                'action' => 'retry sending email',
                'to' => $this->to_address,
                'subject' => $this->subject,
                'attempts_count' => $this->attempts_count,
                'last_attempt' => $this->updated_at,
            ];
            \Yii::error($log);

            if ( ! $this->save()) {
                \Yii::error([
                    'action' => 'save email after failed retry failed',
                    'status' => 'error',
                    'error' => $this->getFirstErrors(),
                ]);
                throw new ServerErrorHttpException(
                    'Unable to save email after failing to retry sending. Error: ' .
                        print_r($this->getFirstErrors(), true),
                    1500649788
                );
            }
        }
    }

    /**
     * Builds a mailer object from $this and returns it
     * @return \yii\mail\MessageInterface
     */
    public function getMessage()
    {
        $mailer = \Yii::$app->mailer->compose(
            [
                'html' => '@common/mail/html',
                'text' => '@common/mail/text'
            ],
            [
                'html' => $this->html_body,
                'text' => $this->text_body
            ]
        );
        $mailer->setFrom(\Yii::$app->params['fromEmail']);
        $mailer->setTo($this->to_address);
        $mailer->setSubject($this->subject);

        /*
         * Conditionally set optional fields
         */
        $setMethods = [
            'setCc' => $this->cc_address,
            'setBcc' => $this->bcc_address,
        ];
        foreach ($setMethods as $method => $value) {
            if ($value) {
                $mailer->$method($value);
            }
        }

        return $mailer;
    }

    /**
     * Attempt to send messages from queue
     * @throws \Exception
     */
    public static function sendQueuedEmail()
    {
        $log = [
            'action' => 'email/sendQueuedEmail',
        ];
        try {
            $batchSize = \Yii::$app->params['emailQueueBatchSize'];
            $queued = self::find()->orderBy(['updated_at' => SORT_ASC])->limit($batchSize)->all();

            $log += [
                'batchSize' => $batchSize,
                'queuedEmails' => count($queued),
                'sentEmails' => 0,
            ];

            if (empty($queued)) {
                // If nothing queued, no need to send log
                return;
            }

            /** @var Email $email */
            foreach ($queued as $email) {
                $email->retry();
                $log['sentEmails']++;
            }
        } catch (\Exception $e) {
            $log += [
                'status' => 'error',
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
            \Yii::error($log);
        }

        // Send log of successful processing
        \Yii::info($log);
    }

    /**
     * If $this has been saved to database, it will be deleted and on failure throw an exception
     * @throws \Exception
     */
    private function removeFromQueue()
    {
        try {
            if ($this->id && ! $this->delete()) {
                throw new \Exception(
                    'Unable to delete email queue entry',
                    1461012183
                );
            }
        } catch (\Exception $e) {
            $log = [
                'action' => 'delete after send',
                'status' => 'failed to delete',
                'error' => $e->getMessage(),
            ];
            \Yii::error($log, 'application');

            throw new \Exception(
                'Unable to delete email queue entry',
                1461012337
            );
        }
    }

//    /** TODO: update for this service
//     * @return array of fields that should be included in responses.
//     */
//    public function fields(): array
//    {
//        $fields = [
//            'uuid',
//            'employee_id',
//            'first_name',
//            'last_name',
//            'display_name' => function ($model) {
//                return $model->display_name ?? "$model->first_name $model->last_name";
//            },
//            'username',
//            'email',
//            'active',
//            'locked',
//        ];
//
//        if ($this->current_password_id !== null) {
//            $fields['password'] = function () {
//                return $this->currentPassword;
//            };
//        }
//
//        return $fields;
//    }
}
