<?php

class EmailCest
{
    public function testQueue_MinimumFields_TextBody(ApiTester $I)
    {
        $I->wantTo('queue an email with minimum fields using a text body');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@example.org',
            'subject' => 'test subject min fields (text body)',
            'text_body' => 'text body',
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testQueue_MinimumFields_HtmlBody(ApiTester $I)
    {
        $I->wantTo('queue an email with minimum fields using an html body');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@example.org',
            'subject' => 'test subject min fields (html body)',
            'html_body' => '<p>html body</p>',
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testQueue_AllowedFields_DelaySeconds(ApiTester $I)
    {
        $I->wantTo('queue an email with allowed fields, delay_seconds');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@example.org',
            'cc_address' => 'testcc@example.org',
            'bcc_address' => 'testbcc@example.org',
            'subject' => 'subject allowed fields',
            'text_body' => 'text body',
            'html_body' => 'html body',
            'delay_seconds' => 10,
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testQueue_AllowedFields_SendAfter(ApiTester $I)
    {
        $I->wantTo('queue an email with allowed fields, send_after');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@example.org',
            'cc_address' => 'testcc@example.org',
            'bcc_address' => 'testbcc@example.org',
            'subject' => 'subject allowed fields',
            'text_body' => 'text body',
            'html_body' => 'html body',
            'send_after' => 1556314645,
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testQueue_AllFields(ApiTester $I)
    {
        $I->wantTo('queue an email with all fields');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'id' => 123,
            'to_address' => 'test@example.org',
            'cc_address' => 'testcc@example.org',
            'bcc_address' => 'testbcc@example.org',
            'subject' => 'subject all fields',
            'text_body' => 'text body',
            'html_body' => 'html body',
            'attempts_count' => 456,
            'created_at' => 11111111,
            'updated_at' => 22222222,
            'error' => 'error message',
            'send_after' => 1556314645,
        ]);
        $I->seeResponseCodeIs(200);
        $I->dontSeeResponseContains('123');
        $I->dontSeeResponseContains('456');
        $I->dontSeeResponseContains('11111111');
        $I->dontSeeResponseContains('22222222');
        $I->dontSeeResponseContains('error message');
    }

    public function testQueue_NoAccessToken(ApiTester $I)
    {
        $I->wantTo('queue an email without a bearer access token');
        $I->sendPOST('/email');
        $I->seeResponseCodeIs(401);
    }

    public function testQueue_InvalidAccessToken(ApiTester $I)
    {
        $I->wantTo('queue an email with an invalid bearer access token');
        $I->haveHttpHeader('Authorization', 'Bearer invalid');
        $I->sendPOST('/email');
        $I->seeResponseCodeIs(401);
    }

    public function testInvalidPath(ApiTester $I)
    {
        $I->wantTo('queue an email using the wrong path');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/invalid');
        $I->seeResponseCodeIs(404);
    }

    public function testInvalidMethodGet(ApiTester $I)
    {
        $I->wantTo('queue an email using a GET');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendGet('/email');
        $I->seeResponseCodeIs(404);
    }

    public function testInvalidMethodDelete(ApiTester $I)
    {
        $I->wantTo('queue an email using a DELETE');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendDELETE('/email');
        $I->seeResponseCodeIs(404);
    }

    public function testInvalidMethodPut(ApiTester $I)
    {
        $I->wantTo('queue an email using a PUT');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPUT('/email');
        $I->seeResponseCodeIs(404);
    }

    public function testQueue_RequiredFieldsMissing_ToAddress(ApiTester $I)
    {
        $I->wantTo('queue an email without the required to_address');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testQueue_RequiredFieldsMissing_Subject(ApiTester $I)
    {
        $I->wantTo('queue an email without the required subject');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@example.org',
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testQueue_RequiredFieldsMissing_TextBody(ApiTester $I)
    {
        $I->wantTo('queue an email without the required text body');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@example.org',
            'subject' => 'subject',
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testQueue_InvalidToAddress(ApiTester $I)
    {
        $I->wantTo('queue an email with an invalid to_address');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test',
            'subject' => 'subject',
            'text_body' => 'text body',
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testQueue_InvalidCcAddress(ApiTester $I)
    {
        $I->wantTo('queue an email with an invalid cc_address');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@example.org',
            'cc_address' => 'testCc',
            'subject' => 'subject',
            'text_body' => 'text body',
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testQueue_InvalidBccAddress(ApiTester $I)
    {
        $I->wantTo('queue an email with an invalid bcc_address');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@example.org',
            'bcc_address' => 'testBcc',
            'subject' => 'subject',
            'text_body' => 'text body',
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testSystemStatus(ApiTester $I)
    {
        $I->wantTo('test system status');
        $I->sendGET('/site/status');
        $I->seeResponseCodeIs(204);
    }
}
