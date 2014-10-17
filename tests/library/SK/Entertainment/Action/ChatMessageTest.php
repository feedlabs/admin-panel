<?php

class SK_Entertainment_Action_ChatMessageTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testExecute() {
        $messages = array('foo', 'bar');
        $targetUser = SKTest_TH::createUser();
        $targetUser->setOnline();
        $sourceUser = SKTest_TH::createUser();
        $sourceUser->getRoles()->add(SK_Role::ENTERTAINER);
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => 'foo'));
        $messageBody = '{' . implode('|', $messages) . '}';
        SKTest_TH::createEntertainmentMessage($messageSet, $messageBody);
        $action = new SK_Entertainment_Action_ChatMessage($targetUser, new SK_Params(array('sourceUser' => $sourceUser,
                                                                                           'messageSet' => $messageSet)), time());
        $this->assertTrue($sourceUser->getChats()->isEmpty());
        $this->assertTrue($targetUser->getChats()->isEmpty());
        $this->assertTrue($targetUser->getOnline());

        $action->execute();

        $this->assertSame(1, $sourceUser->getChats()->getCount());
        $this->assertSame(1, $targetUser->getChats()->getCount());
        /** @var SK_Entity_Chat $chat */
        $chat = $targetUser->getChats()->getItem(0);
        $this->assertEquals($chat, $targetUser->getChats()->getItem(0));
        $chatMessage = $chat->getMessages()->getItem(0);

        // check that second chatMessage by same user is created in the same chat as the first one
        $action->execute();

        $this->assertSame(1, $sourceUser->getChats()->getCount());
        $this->assertSame(1, $targetUser->getChats()->getCount());
        /** @var SK_Entity_Chat $chat */
        $chat = $targetUser->getChats()->getItem(0);
        $this->assertSame(2, $chat->getMessages()->getCount());
        foreach ($chat->getMessages() as $messageModel) {
            $this->assertContains($messageModel['message'], $messages);
            $this->assertNotEquals($messageBody, $messageModel['message']);
        }
    }

    public function testExecuteInvisible() {
        $targetUser = SKTest_TH::createUser();
        $targetUser->setOnline();
        $targetUser->setVisible(false);
        $sourceUser = SKTest_TH::createUser();
        $sourceUser->getRoles()->add(SK_Role::ENTERTAINER);
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => 'foo'));
        SKTest_TH::createEntertainmentMessage($messageSet, 'foo');
        $action = new SK_Entertainment_Action_ChatMessage($targetUser, new SK_Params(array('sourceUser' => $sourceUser,
                                                                                           'messageSet' => $messageSet)), time());
        $this->assertTrue($sourceUser->getChats()->isEmpty());
        $this->assertTrue($targetUser->getChats()->isEmpty());
        $this->assertTrue($targetUser->getOnline());
        $this->assertFalse($targetUser->getVisible());

        $action->execute();

        $this->assertTrue($sourceUser->getChats()->isEmpty());
        $this->assertTrue($targetUser->getChats()->isEmpty());
    }

    public function testExecuteSameUser() {
        $user = SKTest_TH::createUser();
        $user->setOnline();
        $user->getRoles()->add(SK_Role::ENTERTAINER);
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => 'foo'));
        SKTest_TH::createEntertainmentMessage($messageSet);
        $action = new SK_Entertainment_Action_ChatMessage($user, new SK_Params(array('sourceUser' => $user, 'messageSet' => $messageSet)), time());
        $this->assertTrue($user->getChats()->isEmpty());

        $action->execute();

        $this->assertTrue($user->getChats()->isEmpty());
    }

    public function testExecuteBlockedUser() {
        $targetUser = SKTest_TH::createUser();
        $targetUser->setOnline();
        $sourceUser = SKTest_TH::createUser();
        $sourceUser->getRoles()->add(SK_Role::ENTERTAINER);
        $targetUser->getBlockings()->add($sourceUser);
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => 'foo'));
        SKTest_TH::createEntertainmentMessage($messageSet);
        $action = new SK_Entertainment_Action_ChatMessage($targetUser, new SK_Params(array('sourceUser' => $sourceUser,
                                                                                           'messageSet' => $messageSet)), time());
        $this->assertTrue($sourceUser->getChats()->isEmpty());
        $this->assertTrue($targetUser->getChats()->isEmpty());

        $action->execute();

        $this->assertTrue($sourceUser->getChats()->isEmpty());
        $this->assertTrue($targetUser->getChats()->isEmpty());
    }
}
