<?php

namespace common\components;

use Aws\Ses\SesClient;
use yii\mail\BaseMailer;

class Mailer extends BaseMailer
{
    /**
     * @var string message default class name.
     */
    public $messageClass = Message::class;

    /** @var SesClient */
    public $client;

    public function init()
    {
        // the AWS SDK gets credentials from env vars AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY
        $this->client = new SesClient([
            'version' => 'latest',
            'region' => 'us-east-1',
        ]);
    }

    /**
     * @param Message $message
     * @return bool
     */
    protected function sendMessage($message)
    {
        try {
            $result = $this->client->sendEmail([
                'Destination' => [
                    'ToAddresses' => $message->getTo(),
                ],
                'ReplyToAddresses' => $message->getReplyTo(),
                'Source' => $message->getFrom(),
                'Message' => [
                    'Body' => [
                        'Html' => [
                            'Charset' => $message->getCharset(),
                            'Data' => $message->getHtmlBody(),
                        ],
                        'Text' => [
                            'Charset' => $message->getCharset(),
                            'Data' => $message->getTextBody(),
                        ],
                    ],
                    'Subject' => [
                        'Charset' => $message->getCharset(),
                        'Data' => $message->getSubject(),
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            \Yii::error([
                'action' => 'sendMessage',
                'type' => get_class($e),
                'message' => $e->getMessage()
            ]);
            return false;
        }
        \Yii::info('message sent, id: ' . $result['MessageId']);
        return true;
    }
}