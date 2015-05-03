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
        $convoService->createConversation(1, []);
    }

    /**
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testGetConversation()
    {
        $users = \App\Model\User::all();
        // create convo
        $convoService = new ConvosService(new ConvosRepository());
        $convo = $this->_newConvo($users, $convoService);

        // this should be fine
        $convoService->getConversation($convo->id, $users->get(0)->id);

        // this returns 404
        $convoService->getConversation($convo->id, $this->faker->numberBetween($min = 1000, $max = 9000));
    }

    private function _newConvo($users, $convoService)
    {
        $subject = $this->faker->text(140);
        $recipient = $users->get(1)->id;
        $body = $this->faker->paragraph();

        $convo = $convoService->createConversation($users->get(0)->id, [
            'subject' => $subject,
            'recipient' => $recipient,
            'body' => $body
        ]);

        return $convo;
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

    /**
     * @expectedException App\Model\ConvosException
     */
    public function testCreateSameUser()
    {
        $users = \App\Model\User::all();
        // setting user 1 to be the same as user 0 to test the validation
        $users[1] = $users[0];
        // create convo
        $convoService = new ConvosService(new ConvosRepository());
        $this->_newConvo($users, $convoService); // Boom!
    }

    public function testAddMessage()
    {
        $users = \App\Model\User::all();
        // create convo
        $convoService = new ConvosService(new ConvosRepository());
        $convo = $this->_newConvo($users, $convoService);
        // add message
        $message = $convoService->addConversationMessage(
            $convo->id,
            $users->get(1)->id,
            [
                'body' => $this->faker->paragraph()
            ]
        );

        // check message in conversation
        $this->assertEquals(2, $convo->fresh()->messages->count());

        // check participants details
        foreach ($convo->fresh()->participants as $participant) {
            if ($participant->id == $users->get(1)->id) {
                $this->assertTrue($participant->is_read);
            } else {
                $this->assertFalse($participant->is_read);
            }
        }
    }

    /**
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testGetMessages()
    {
        $users = \App\Model\User::all();
        // create convo
        $convoService = new ConvosService(new ConvosRepository());
        $convo = $this->_newConvo($users, $convoService);
        // create 9 messages... one is already created by the nw convo call
        for ($x = 0; $x <= 8; $x++) {
            $convoService->addConversationMessage($convo->id, $users->get($x % 2)->id, [
                'body' => $this->faker->paragraph()
            ]);
        }

        $result = $convoService->getConversationMessages($convo->id, $users->get(0)->id);
        $this->assertEquals(10, sizeof($result['messages']));
        $this->assertEquals(10, $result['pagination']['count']);
        $this->assertEquals(1, $result['pagination']['page']);
        $this->assertEquals(25, $result['pagination']['limit']);


        $result = $convoService->getConversationMessages($convo->id, $users->get(1)->id, $limit = 2);
        $this->assertEquals(10, $result['pagination']['count']);
        $this->assertEquals(2, sizeof($result['messages']));

        $result = $convoService->getConversationMessages($convo->id, $users->get(0)->id, $limit = 3, $page = 2);
        $this->assertEquals(10, $result['pagination']['count']);
        $this->assertEquals(3, sizeof($result['messages']));

        $result = $convoService->getConversationMessages(
            $convo->id, $users->get(1)->id, $limit = 1, $page = 1, $until = Carbon::yesterday()->toIso8601String()
        );
        $this->assertEquals(0, $result['pagination']['count']);
        $this->assertEquals(0, sizeof($result['messages']));

        // no conversation... exception!
        $result = $convoService->getConversationMessages($convo->id, $this->faker->numberBetween($min = 1000, $max = 9000));
    }

    public function testGetConversations()
    {
        $users = \App\Model\User::all();
        $convoService = new ConvosService(new ConvosRepository());
        // create 10 convos
        $convos = [];
        for ($x = 0; $x <= 9; $x++) {
            $convos[] = $this->_newConvo($users, $convoService);
        }
        // get the list for user 1
        $result = $convoService->getConversations($users->get(0)->id);
        $this->assertEquals(10, $result['pagination']['count']);

        // get the list for user 2
        $result = $convoService->getConversations($users->get(0)->id);
        $this->assertEquals(10, $result['pagination']['count']);

        // sleep 1 sec
        sleep(1);

        // I add a message to the last convo and it should go on top of the list
        $convoService->addConversationMessage($convos[0]->id, $users->get(1)->id, [
            'body' => $this->faker->paragraph()
        ]);

        $result = $convoService->getConversations($users->get(0)->id, $limit = 1);
        $this->assertEquals($convos[0]->id, $result['conversations'][0]->id);
        $result = $convoService->getConversations($users->get(1)->id, $limit = 1);
        $this->assertEquals($convos[0]->id, $result['conversations'][0]->id);
    }

    public function testDeleteConversation()
    {
        $users = \App\Model\User::all();
        $convoService = new ConvosService(new ConvosRepository());

        // create 1 convos
        $convo = $this->_newConvo($users, $convoService);

        // delete the convo for user 1
        $convoService->deleteConversation($convo->id, $users->get(0)->id);

        // get the list, only user 2 should have this conversation
        $result = $convoService->getConversations($users->get(0)->id, $limit = 1);
        $this->assertEquals(0, $result['pagination']['count']);
        $result = $convoService->getConversations($users->get(1)->id, $limit = 1);
        $this->assertEquals(1, $result['pagination']['count']);
    }

    /**
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testDeleteMessage()
    {
        $users = \App\Model\User::all();
        $convoService = new ConvosService(new ConvosRepository());

        // create 1 convos
        $convo = $this->_newConvo($users, $convoService);

        // user 2 tries to delete a message and throws an exeption
        $convoService->deleteConversationMessage(
            $convo->id,
            $users->get(1)->id,
            $convo->messages->get(0)->id
        );

        // user 1 deletes and it's all fine
        $convoService->deleteConversationMessage(
            $convo->id,
            $users->get(0)->id,
            $convo->messages->get(0)->id
        );

        // no more messages
        $result = $convoService->getConversationMessages($convo->id, $users->get(0)->id);
        $this->assertEquals(0, $result['pagination']['count']);

        $result = $convoService->getConversationMessages($convo->id, $users->get(1)->id);
        $this->assertEquals(0, $result['pagination']['count']);
    }

    public function testMarkConversationRead()
    {
        $users = \App\Model\User::all();
        $convoService = new ConvosService(new ConvosRepository());

        // create 1 convos
        $convo = $this->_newConvo($users, $convoService);

        // user 1 marks it as read
        $convoService->updateConversation(
            $convo->id,
            $users->get(0)->id,
            ['is_read' => true]
        );

        // user 2 don't
        $convoService->updateConversation(
            $convo->id,
            $users->get(1)->id,
            ['is_read' => false]
        );

        // get the list for user 1
        $result = $convoService->getConversations($users->get(0)->id, $limit = 1);
        $this->assertEquals(1, $result['conversations'][0]->is_read);

        $result = $convoService->getConversations($users->get(1)->id, $limit = 1);
        $this->assertEquals(0, $result['conversations'][0]->is_read);

    }


}