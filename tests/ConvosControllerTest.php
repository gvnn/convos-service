<?php

class ConvosControllerTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function setUp()
    {
        parent::setUp();
        $this->faker = Faker\Factory::create();
    }

    public function testConversationNotFound()
    {
        $token = $this->_getUserToken('foo@domain.com', 'test');

        $response = $this->call('GET', '/api/v1/convos/1', [], [], [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token->access_token,
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json']);

        $this->assertEquals(404, $response->getStatusCode());
    }

    private function _getUserToken($username, $password)
    {
        $response = $this->call('POST', '/oauth/access_token', [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'client_id' => 'client1id',
            'client_secret' => 'client1secret'
        ]);

        $jsonResponse = $response->getContent();
        return json_decode($jsonResponse);
    }

}