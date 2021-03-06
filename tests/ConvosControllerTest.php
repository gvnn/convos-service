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

    public function testNotFound()
    {
        $headers = $this->getHeaders('foo@domain.com', 'test');
        $response = $this->call('GET', '/api/v1/convos/1', [], [], [], $headers);
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->call('PUT', '/api/v1/convos/1', ['is_read' => true], [], [], $headers);
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->call('DELETE', '/api/v1/convos/1', [], [], [], $headers);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testBadRequest()
    {
        $headers = $this->getHeaders('foo@domain.com', 'test');

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