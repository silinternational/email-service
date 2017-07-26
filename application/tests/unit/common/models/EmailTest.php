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
    private $stubToAddress = 'test@example.org';
    private $stubCcAddress = 'testCc@example.org';
    private $stubBccAddress = 'testBcc@example.org';
    private $stubSubject;
    private $stubTextBody = 'email content as text';
    private $stubHtmlBody = '<p>email content as html</p>';

    protected function _before()
    {
        Email::deleteAll();

        $this->stubSubject = 'tested at '.microtime();
    }

    public function testCreateMassAssignment_MinimumFields()
    {
        $email = new Email();

        $email->attributes = [
            'to_address' => $this->stubToAddress,
            'subject' => $this->stubSubject,
            //TODO: need to have a body as well.
        ];

        $this->assertTrue($email->save(), current($email->getFirstErrors()));

        $this->assertNotNull($email->id);
        $this->assertEquals($this->stubToAddress, $email->to_address);
        $this->assertNull($email->cc_address);
        $this->assertNull($email->bcc_address);
        $this->assertEquals($this->stubSubject, $email->subject);
        $this->assertNull($email->text_body);
        $this->assertNull($email->html_body);
        $this->assertEquals(0, $email->attempts_count);
        $this->assertNotNull($email->updated_at);
        $this->assertNotNull($email->created_at);
        $this->assertNull($email->error);
    }

    public function testCreateMassAssignment_AllowedFields()
    {
        $email = new Email();

        $email->attributes = [
            'to_address' => $this->stubToAddress,
            'cc_address' => $this->stubCcAddress,
            'bcc_address' => $this->stubBccAddress,
            'subject' => $this->stubSubject,
            'text_body' => $this->stubTextBody,
            'html_body' => $this->stubHtmlBody,
        ];

        $this->assertTrue($email->save(), current($email->getFirstErrors()));

        $this->assertNotNull($email->id);
        $this->assertEquals($this->stubToAddress, $email->to_address);
        $this->assertEquals($this->stubCcAddress, $email->cc_address);
        $this->assertEquals($this->stubBccAddress, $email->bcc_address);
        $this->assertEquals($this->stubSubject, $email->subject);
        $this->assertEquals($this->stubTextBody, $email->text_body);
        $this->assertEquals($this->stubHtmlBody, $email->html_body);
        $this->assertEquals(0, $email->attempts_count);
        $this->assertNotNull($email->updated_at);
        $this->assertNotNull($email->created_at);
        $this->assertNull($email->error);
    }

    public function testCreateMassAssignment_AllFields()
    {
        $stubId = 123;
        $stubUpdateAt = 22222222;
        $stubCreatedAt = 11111111;
        $stubErrorMessage = 'stub error message';

        $email = new Email();

        $email->attributes = [
            'id' => $stubId,
            'to_address' => $this->stubToAddress,
            'cc_address' => $this->stubCcAddress,
            'bcc_address' => $this->stubBccAddress,
            'subject' => $this->stubSubject,
            'text_body' => $this->stubTextBody,
            'html_body' => $this->stubHtmlBody,
            'attempts_count' => 111,
            'updated_at' => $stubUpdateAt,
            'created_at' => $stubCreatedAt,
            'error' => $stubErrorMessage,
        ];

        $this->assertTrue($email->save(), current($email->getFirstErrors()));

        $this->assertNotEquals($stubId, $email->id);
        $this->assertEquals($this->stubToAddress, $email->to_address);
        $this->assertEquals($this->stubCcAddress, $email->cc_address);
        $this->assertEquals($this->stubBccAddress, $email->bcc_address);
        $this->assertEquals($this->stubSubject, $email->subject);
        $this->assertEquals($this->stubTextBody, $email->text_body);
        $this->assertEquals($this->stubHtmlBody, $email->html_body);
        $this->assertEquals(0, $email->attempts_count);
        $this->assertNotEquals($stubUpdateAt, $email->updated_at);
        $this->assertNotEquals($stubCreatedAt, $email->created_at);
        $this->assertNotEquals($stubErrorMessage, $email->error);
    }

    public function testSend()
    {
        $initialEmailQueueCount = Email::find()->count();
        $initialEmailSentCount = $this->countMailFiles();

        $email = new Email();

        $email->attributes = [
            'to_address' => $this->stubToAddress,
            'cc_address' => $this->stubCcAddress,
            'bcc_address' => $this->stubBccAddress,
            'subject' => $this->stubSubject,
            'text_body' => $this->stubTextBody,
            'html_body' => $this->stubHtmlBody,
        ];

        $this->assertTrue($email->save(), current($email->getFirstErrors()));

        $this->assertEquals($initialEmailQueueCount + 1, Email::find()->count(), 'emails in db did not increase by one after saving email');

        $email->send();

        $this->assertEquals($initialEmailSentCount + 1, $this->countMailFiles(), 'sent emails count did not increase by one after sending email');
        $this->assertEquals($initialEmailQueueCount, Email::find()->count(), 'emails in db did not decrease by one after sending email');
    }

    public function testRetry()
    {
        $initialEmailQueueCount = Email::find()->count();
        $initialEmailSentCount = $this->countMailFiles();

        $email = new Email();

        $email->attributes = [
            'to_address' => $this->stubToAddress,
            'cc_address' => $this->stubCcAddress,
            'bcc_address' => $this->stubBccAddress,
            'subject' => $this->stubSubject,
            'text_body' => $this->stubTextBody,
            'html_body' => $this->stubHtmlBody,
        ];

        $this->assertTrue($email->save(), current($email->getFirstErrors()));

        $this->assertEquals($initialEmailQueueCount + 1, Email::find()->count(), 'emails in db did not increase by one after saving email');

        $email->retry();

        $this->assertEquals($initialEmailSentCount + 1, $this->countMailFiles(), 'sent emails count did not increase by one after sending email');
        $this->assertEquals($initialEmailQueueCount, Email::find()->count(), 'emails in db did not decrease by one after sending email');
    }

    public function testGetMessageRendersAsHtmlAndText()
    {
        $email = new Email();

        $email->attributes = [
            'to_address' => $this->stubToAddress,
            'cc_address' => $this->stubCcAddress,
            'bcc_address' => $this->stubBccAddress,
            'subject' => $this->stubSubject,
            'text_body' => $this->stubTextBody,
            'html_body' => $this->stubHtmlBody,
        ];

        $this->assertTrue($email->save(), current($email->getFirstErrors()));

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

        // create 5 queued emails
        for ($i = 0; $i < 5; $i++) {
            $email = new Email();

            $email->attributes = [
                'to_address' => $this->stubToAddress,
                'cc_address' => $this->stubCcAddress,
                'bcc_address' => $this->stubBccAddress,
                'subject' => $this->stubSubject." $i",
                'text_body' => $this->stubTextBody,
                'html_body' => $this->stubHtmlBody,
            ];

            $this->assertTrue($email->save(), current($email->getFirstErrors()));
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
