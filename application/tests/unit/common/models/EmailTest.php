<?php

namespace tests\unit\common\models;

use common\models\Email;
use Sil\Codeception\TestCase\Test;

include_once __DIR__ . '/../../../_support/UnitTester.php';

/**
 * Class EmailTest
 * @package tests\unit\common\models
 * @property \UnitTester tester
 */
class EmailTest extends Test
{
    protected function _before()
    {
        Email::deleteAll();
    }

    public function testCreateMassAssignment_MinimumFields()
    {
        $timestamp = microtime();
        $attributes = [
            'to_address' => 'test@test.com',
            'subject' => (string)$timestamp,
            //TODO: need to have a body as well.
        ];

        $email = new Email();

        $email->attributes = $attributes;
        $this->assertTrue(
            $email->save(),
            'Failed to save with minimum fields: ' . print_r($email->getFirstErrors(), true)
        );

        $this->assertEquals($attributes['to_address'], $email->to_address);
        $this->assertEquals($attributes['subject'], $email->subject);
        $this->assertEquals(0, $email->attempts_count);

        $this->assertNotNull($email->id);
        $this->assertNotNull($email->created_at);
        $this->assertNotNull($email->updated_at);

        $this->assertNull($email->cc_address);
        $this->assertNull($email->bcc_address);
        $this->assertNull($email->text_body);
        $this->assertNull($email->html_body);
        $this->assertNull($email->error);
    }

    public function testCreateMassAssignment_AllowedFields()
    {
        $timestamp = microtime();
        $attributes = [
            'to_address' => 'test@test.com',
            'cc_address' => 'testcc@test.com',
            'bcc_address' => 'testbcc@test.com',
            'subject' => $timestamp,
            'text_body' => 'text body',
            'html_body' => 'html body',
        ];

        $email = new Email();
        $email->attributes = $attributes;
        $this->assertTrue(
            $email->save(),
            'Failed to save with allowed fields: ' . print_r($email->getFirstErrors(), true)
        );

        $this->assertEquals($attributes['to_address'], $email->to_address);
        $this->assertEquals($attributes['cc_address'], $email->cc_address);
        $this->assertEquals($attributes['bcc_address'], $email->bcc_address);
        $this->assertEquals($attributes['subject'], $email->subject);
        $this->assertEquals($attributes['text_body'], $email->text_body);
        $this->assertEquals($attributes['html_body'], $email->html_body);
        $this->assertEquals(0, $email->attempts_count);

        $this->assertNotNull($email->id);
        $this->assertNotNull($email->created_at);
        $this->assertNotNull($email->updated_at);

        $this->assertNull($email->error);
    }

    public function testCreateMassAssignment_AllFields()
    {
        $this->markTestSkipped('Skipping until scenarios are built to prevent mass assignment of unsafe attributes');
        $timestamp = microtime();
        $attributes = [
            'id' => 123,
            'to_address' => 'test@test.com',
            'cc_address' => 'testcc@test.com',
            'bcc_address' => 'testbcc@test.com',
            'subject' => $timestamp,
            'text_body' => 'text body',
            'html_body' => 'html body',
            'attempts_count' => 111,
            'created_at' => 11111111,
            'updated_at' => 22222222,
            'error' => 'error message',
        ];

        $email = new Email();
        $email->attributes = $attributes;
        $this->assertTrue(
            $email->save(),
            'Failed to save with all fields: ' . print_r($email->getFirstErrors(), true)
        );

        $this->assertEquals($attributes['to_address'], $email->to_address);
        $this->assertEquals($attributes['cc_address'], $email->cc_address);
        $this->assertEquals($attributes['bcc_address'], $email->bcc_address);
        $this->assertEquals($attributes['subject'], $email->subject);
        $this->assertEquals($attributes['text_body'], $email->text_body);
        $this->assertEquals($attributes['html_body'], $email->html_body);
        $this->assertEquals(0, $email->attempts_count);

        $this->assertNotEquals($attributes['id'], $email->id);
        $this->assertNotEquals($attributes['attempts_count'], $email->attempts_count);
        $this->assertNotEquals($attributes['created_at'], $email->created_at);
        $this->assertNotEquals($attributes['updated_at'], $email->updated_at);
        $this->assertNotEquals($attributes['error'], $email->error);
    }

    public function testSend()
    {
        $initialEmailQueueCount = Email::find()->count();
        $initialEmailSentCount = $this->countMailFiles();

        $timestamp = microtime();
        $attributes = [
            'to_address' => 'test@test.com',
            'cc_address' => 'testcc@test.com',
            'bcc_address' => 'testbcc@test.com',
            'subject' => $timestamp,
            'text_body' => 'text body',
            'html_body' => 'html body',
        ];

        $email = new Email();
        $email->attributes = $attributes;
        $this->assertTrue(
            $email->save(),
            'Failed to save when creating email to send: ' . print_r($email->getFirstErrors(), true)
        );

        $this->assertEquals($initialEmailQueueCount+1, Email::find()->count(), 'emails in db did not increase by one after saving email');
        $email->send();
        $this->assertEquals($initialEmailSentCount+1, $this->countMailFiles(), 'sent emails count did not increase by one after sending email');
        $this->assertEquals($initialEmailQueueCount, Email::find()->count(), 'emails in db did not decrease by one after sending email');
    }

    public function testRetry()
    {
        $initialEmailQueueCount = Email::find()->count();
        $initialEmailSentCount = $this->countMailFiles();

        $timestamp = microtime();
        $attributes = [
            'to_address' => 'test@test.com',
            'cc_address' => 'testcc@test.com',
            'bcc_address' => 'testbcc@test.com',
            'subject' => $timestamp,
            'text_body' => 'text body',
            'html_body' => 'html body',
        ];

        $email = new Email();
        $email->attributes = $attributes;
        $this->assertTrue(
            $email->save(),
            'Failed to save when creating email to send: ' . print_r($email->getFirstErrors(), true)
        );

        $this->assertEquals($initialEmailQueueCount+1, Email::find()->count(), 'emails in db did not increase by one after saving email');
        $email->retry();
        $this->assertEquals($initialEmailSentCount+1, $this->countMailFiles(), 'sent emails count did not increase by one after sending email');
        $this->assertEquals($initialEmailQueueCount, Email::find()->count(), 'emails in db did not decrease by one after sending email');
    }

    public function testGetMessageRendersAsHtmlAndText()
    {
        $timestamp = microtime();
        $attributes = [
            'to_address' => 'test@test.com',
            'cc_address' => 'testcc@test.com',
            'bcc_address' => 'testbcc@test.com',
            'subject' => $timestamp,
            'text_body' => 'text body',
            'html_body' => '<b>html body</b>',
        ];

        $email = new Email();
        $email->attributes = $attributes;
        $this->assertTrue(
            $email->save(),
            'Failed to save with allowed fields: ' . print_r($email->getFirstErrors(), true)
        );

        $email->send();
        /** @var yii\mail\Message[] $sent */
        $sent = $this->tester->grabSentEmails();
        $asString = $sent[0]->toString();
        $this->assertContains('text/plain', $asString);
        $this->assertContains('text/html', $asString);
        $this->assertContains('<!DOCTYPE html PUBLIC', $asString);
    }

    public function testSendQueuedEmails()
    {
        $initialEmailQueueCount = Email::find()->count();
        $initialEmailSentCount = $this->countMailFiles();

        $timestamp = microtime();
        $attributes = [
            'to_address' => 'test@test.com',
            'cc_address' => 'testcc@test.com',
            'bcc_address' => 'testbcc@test.com',
            'subject' => $timestamp,
            'text_body' => 'text body',
            'html_body' => '<b>html body</b>',
        ];

        // create 5 queued emails
        for ($i=0; $i<5; $i++) {
            $email = new Email();
            $email->attributes = $attributes;
            $this->assertTrue(
                $email->save(),
                'Failed to save with allowed fields: ' . print_r($email->getFirstErrors(), true)
            );
        }

        $this->assertEquals($initialEmailQueueCount + 5, Email::find()->count());
        Email::sendQueuedEmail();
        $this->assertEquals(0, Email::find()->count());
        $this->assertEquals($initialEmailSentCount + 5, $this->countMailFiles());

    }


    public function countMailFiles()
    {
        return count($this->tester->grabSentEmails());
    }
}
