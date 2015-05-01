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
        $headers = $this->_getHeaders('foo@domain.com', 'test');
        $response = $this->call('GET', '/api/v1/convos/1', [], [], [], $headers);
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->call('PUT', '/api/v1/convos/1', ['is_read' => true], [], [], $headers);
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->call('DELETE', '/api/v1/convos/1', [], [], [], $headers);
        $this->assertEquals(404, $response->getStatusCode());
    }

    private function _getHeaders($username, $password)
    {
        $token = $this->_getUserToken($username, $password);
        return [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token->access_token,
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json'
        ];
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
        return $this->parseJson($response);
    }

    public function testBadRequest()
    {
        $headers = $this->_getHeaders('foo@domain.com', 'test');

        $response = $this->call('POST', '/api/v1/convos', [], [], [], $headers);
        $this->assertEquals(400, $response->getStatusCode());

        $json = $this->parseJson($response);

        // check one of the errors
        $this->assertObjectHasAttribute('subject', $json->error_description);
        $this->assertObjectHasAttribute('recipient', $json->error_description);
        $this->assertObjectHasAttribute('body', $json->error_description);

        $response = $this->call('PUT', '/api/v1/convos/1', [], [], [], $headers);
        $this->assertEquals(400, $response->getStatusCode());
        $json = $this->parseJson($response);
        $this->assertObjectHasAttribute('is_read', $json->error_description);

    }

}