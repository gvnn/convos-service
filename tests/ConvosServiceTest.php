<?php

use App\Repositories\ConvosRepository;
use App\Services\ConvosService;
use Carbon\Carbon;

class ConvosServiceTest extends TestCase
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
        $convo = $this->_newConvo($users, $convoService);

        $this->assertEquals($users->get(0)->id, $convo->created_by);
        $this->assertEquals(2, $convo->participants->count());
        $this->assertEquals(1, $convo->messages->count());

        $this->assertEquals($users->get(0)->id, $convo->messages->get(0)->user_id);

        $this->assertEquals($users->get(0)->id, $convo->participants->get(0)->user_id);
        $this->assertEquals($users->get(1)->id, $convo->participants->get(1)->user_id);
        $this->assertTrue($convo->participants->get(0)->is_creator);
        $this->assertFalse($convo->participants->get(1)->is_creator);
    }

    private function _newConvo($users, $convoService)
    {
        $subject = $this->faker->text(140);
        $recipient = $users->get(1)->id;
        $body = $this->faker->paragraph();

        $convo = $convoService->create($users->get(0)->id, [
            'subject' => $subject,
            'recipient' => $recipient,
            'body' => $body
        ]);

        return $convo;
    }

    public function testAddMessage()
    {
        $users = \App\Model\User::all();
        // create convo
        $convoService = new ConvosService(new ConvosRepository());
        $convo = $this->_newConvo($users, $convoService);
        // add message
        $message = $convoService->addMessage($convo->id, [
            'user_id' => $users->get(1)->id,
            'body' => $this->faker->paragraph()
        ]);

        // check message in conversation
        $this->assertEquals(2, $convo->messages->count());

        // check participants details
        foreach ($convo->participants as $participant) {
            if ($participant->id == $users->get(1)->id) {
                $this->assertTrue($participant->is_read);
            } else {
                $this->assertFalse($participant->is_read);
            }
        }
    }

    public function testGetMessages()
    {
        $users = \App\Model\User::all();
        // create convo
        $convoService = new ConvosService(new ConvosRepository());
        $convo = $this->_newConvo($users, $convoService);
        // create 9 messages... one is already created by the nw convo call
        for ($x = 0; $x <= 8; $x++) {
            $convoService->addMessage($convo->id, [
                'user_id' => $users->get($x % 2)->id,
                'body' => $this->faker->paragraph()
            ]);
        }

        $result = $convoService->getConvoMessages($convo->id);
        $this->assertEquals(10, sizeof($result['messages']));
        $this->assertEquals(10, $result['pagination']['count']);
        $this->assertEquals(1, $result['pagination']['page']);
        $this->assertEquals(25, $result['pagination']['limit']);


        $result = $convoService->getConvoMessages($convo->id, $limit = 2);
        $this->assertEquals(10, $result['pagination']['count']);
        $this->assertEquals(2, sizeof($result['messages']));

        $result = $convoService->getConvoMessages($convo->id, $limit = 3, $page = 2);
        $this->assertEquals(10, $result['pagination']['count']);
        $this->assertEquals(3, sizeof($result['messages']));

        $result = $convoService->getConvoMessages($convo->id, $limit = 1, $page = 1, $until = Carbon::yesterday()->toIso8601String());
        $this->assertEquals(0, $result['pagination']['count']);
        $this->assertEquals(0, sizeof($result['messages']));
    }

}