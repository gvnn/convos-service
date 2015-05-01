<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    /**
     * Default preparation for each test
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->prepareForTests();
    }

    /**
     * Migrates the database and set the mailer to 'pretend'.
     * This will cause the tests to run quickly.
     *
     */
    private function prepareForTests()
    {
        Artisan::call('migrate');
        $this->seed();
        Mail::pretend(true);
    }

    protected function getHeaders($username, $password)
    {
        $token = $this->getUserToken($username, $password);
        return [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token->access_token,
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json'
        ];
    }

    protected function getUserToken($username, $password)
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

    protected function parseJson($response)
    {
        $jsonResponse = $response->getContent();
        return json_decode($jsonResponse);
    }
}
