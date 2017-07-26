<?php

class EmailCest
{
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

    public function testQueue_MinimumFields_TextBody(ApiTester $I)
    {
        $I->wantTo('queue an email with minimum fields using a text body');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@test.com',
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
            'to_address' => 'test@test.com',
            'subject' => 'test subject min fields (html body)',
            'html_body' => '<p>html body</p>',
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testQueue_AllowedFields(ApiTester $I)
    {
        $I->wantTo('queue an email with allowed fields');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@test.com',
            'cc_address' => 'testcc@test.com',
            'bcc_address' => 'testbcc@test.com',
            'subject' => 'subject allowed fields',
            'text_body' => 'text body',
            'html_body' => 'html body',
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testQueue_AllFields(ApiTester $I)
    {
        $I->wantTo('queue an email with all fields');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'id' => 123,
            'to_address' => 'test@test.com',
            'cc_address' => 'testcc@test.com',
            'bcc_address' => 'testbcc@test.com',
            'subject' => 'subject all fields',
            'text_body' => 'text body',
            'html_body' => 'html body',
            'attempts_count' => 111,
            'created_at' => 11111111,
            'updated_at' => 22222222,
            'error' => 'error message',
        ]);
        $I->seeResponseCodeIs(200);
//        TODO: need to assert values in db are accurate
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

    public function testInvalidPath(ApiTester $I)
    {
        $I->wantTo('queue an email using the wrong path');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/invalid');
        $I->seeResponseCodeIs(404);
    }

    public function testSystemStatus(ApiTester $I)
    {
        $I->wantTo('test system status');
        $I->sendGET('/site/status');
        $I->seeResponseCodeIs(204);
    }
}
