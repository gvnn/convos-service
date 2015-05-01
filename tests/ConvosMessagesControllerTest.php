<?php

class ConvosMessagesControllerTest extends TestCase
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

    public function testNotFound()
    {
        $headers = $this->getHeaders('foo@domain.com', 'test');
        $response = $this->call('GET', '/api/v1/convos/1/messages', [], [], [], $headers);
        $this->assertEquals(404, $response->getStatusCode());

        $headers = $this->getHeaders('foo@domain.com', 'test');
        $response = $this->call('POST', '/api/v1/convos/1/messages', [
            'body' => $this->faker->paragraph()
        ], [], [], $headers);
        $this->assertEquals(404, $response->getStatusCode());

        $headers = $this->getHeaders('foo@domain.com', 'test');
        $response = $this->call('DELETE', '/api/v1/convos/1/messages/1', [], [], [], $headers);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testBadRequest()
    {
        $headers = $this->getHeaders('foo@domain.com', 'test');

        $response = $this->call('POST', '/api/v1/convos/1/messages', [], [], [], $headers);
        $this->assertEquals(400, $response->getStatusCode());

        $json = $this->parseJson($response);

        // check one of the errors
        $this->assertObjectHasAttribute('body', $json->error_description);

    }
}