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

}
