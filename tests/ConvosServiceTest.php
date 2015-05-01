<?php

use App\Repositories\ConvosRepository;
use App\Services\ConvosService;

class ConvosServiceTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testContructor()
    {
        $convoService = new ConvosService(new ConvosRepository());
    }

    /**
     * @expectedException App\Model\ConvosException
     */
    public function testCreateValidation()
    {
        $convoService = new ConvosService(new ConvosRepository());
        $convoService->create(1, []);
    }

    public function testCreate()
    {
        $users = \App\Model\User::all();

        $convoService = new ConvosService(new ConvosRepository());

        $faker = Faker\Factory::create();
        $subject = $faker->text(140);
        $recipient = $users->get(1)->id;
        $body = $faker->paragraph();

        $convo = $convoService->create($users->get(0)->id, [
            'subject' => $subject,
            'recipient' => $recipient,
            'body' => $body
        ]);

        $this->assertTrue($convo->subject === $subject);
        $this->assertEquals($users->get(0)->id, $convo->created_by);
        $this->assertEquals(2, $convo->participants->count());
        $this->assertEquals(1, $convo->messages->count());

        $this->assertEquals($users->get(0)->id, $convo->messages->get(0)->user_id);

        $this->assertEquals($users->get(0)->id, $convo->participants->get(0)->user_id);
        $this->assertEquals($users->get(1)->id, $convo->participants->get(1)->user_id);
        $this->assertTrue($convo->participants->get(0)->is_creator);
        $this->assertFalse($convo->participants->get(1)->is_creator);
    }
}