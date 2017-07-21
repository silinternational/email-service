<?php

namespace common\models;

use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

class Email extends EmailBase
{
    public function rules()
    {
        return ArrayHelper::merge(
            [
                [
                    'attempts_count', 'default', 'value' => 0,
                ],
                [
                    'created_at', 'default', 'value' => time(),
                ],
                [
                    ['to_address', 'cc_address', 'bcc_address'], 'email',
                ],
            ],
            parent::rules()
        );
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
    private function getMessage()
    {
        $mailer = \Yii::$app->mailer->compose();
        $mailer->setFrom(\Yii::$app->params['fromEmail']);
        $mailer->setTo($this->to_address);
        $mailer->setSubject($this->subject);

        // @todo render text and html before setting
        $mailer->setTextBody($this->text_body);

        /*
         * Conditionally set optional fields
         */
        $setMethods = [
            'setCc' => $this->cc_address,
            'setBcc' => $this->bcc_address,
            'setHtmlBody' => $this->html_body,
        ];
        foreach ($setMethods as $method => $value) {
            if ($value) {
                $mailer->$method($value);
            }
        }

        return $mailer;
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


}