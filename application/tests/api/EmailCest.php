<?php

class EmailCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
    }

    public function testQueue_NoAccessToken(ApiTester $I)
    {
        $I->wantTo('queue an email without a bearer access token');
        $I->sendPOST('/email', [
            'to_address' => 'test@test.com',
            'subject' => 'test subject',
            'text_body' => 'text body',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function testQueue_InvalidAccessToken(ApiTester $I)
    {
        $I->wantTo('queue an email with an invalid bearer access token');
        $I->haveHttpHeader('Authorization', 'Bearer invalid');
        $I->sendPOST('/email', [
            'to_address' => 'test@test.com',
            'subject' => 'test subject',
            'text_body' => 'text body',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function testQueue_MinimumFields(ApiTester $I)
    {
        $I->wantTo('queue an email with minimum fields');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'to_address' => 'test@test.com',
            'subject' => 'test subject',
            'text_body' => 'text body',
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
            'subject' => 'subject',
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
            'subject' => 'subject',
            'text_body' => 'text body',
            'html_body' => 'html body',
            'attempts_count' => 111,
            'created_at' => 11111111,
            'updated_at' => 22222222,
            'error' => 'error message',
        ]);
        $I->seeResponseCodeIs(200);
    }

    public function testQueue_InvalidToAddress(ApiTester $I)
    {
        $I->wantTo('queue an email with an invalid to_address');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/email', [
            'id' => 123,
            'to_address' => 'test',
            'subject' => 'subject',
            'text_body' => 'text body',
        ]);
        $I->seeResponseCodeIs(422);
    }

    public function testInvalidPath(ApiTester $I)
    {
        $I->wantTo('queue an email with an invalid to_address');
        $I->haveHttpHeader('Authorization', 'Bearer abc123');
        $I->sendPOST('/invalid', [
            'id' => 123,
            'to_address' => 'test',
            'subject' => 'subject',
            'text_body' => 'text body',
        ]);
        $I->seeResponseCodeIs(404);
    }

    public function testSystemStatus(ApiTester $I)
    {
        $I->wantTo('test system status');
        $I->sendPOST('/site/status');
        $I->seeResponseCodeIs(204);
    }
}
