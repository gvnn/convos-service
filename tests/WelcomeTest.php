<?php

class WelcomeTest extends TestCase
{

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testWelcomePage()
    {
        $response = $this->call('GET', '/');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNotAllowed()
    {
        $response = $this->call('PUT', '/api/v1/convos/1/messages/2');
        $this->assertEquals(405, $response->getStatusCode());
    }

}
