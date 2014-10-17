<?php

class SK_Entity_ConversationMessageTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testGetText() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2)));
        $message = SK_Entity_ConversationMessage_Text::createStatic(array('conversation' => $conversation, 'user' => $user1, 'text' => 'hallo'));
        $this->assertEquals('hallo', $message->getText());
        $this->_checkConsistency();
    }

    protected function _checkConsistency() {
        if (!empty(CM_Config::get()->duplicateConversationsInMongoDB)) {
            $conversationsCli = new SK_Conversations_Cli();
            $this->assertTrue($conversationsCli->check());
        }
    }

    public function testGetUserIfExists() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2)));
        /** @var SK_Entity_ConversationMessage_Text $message */
        $message = SK_Entity_ConversationMessage_Text::createStatic(array('conversation' => $conversation, 'user' => $user2, 'text' => 'hallo'));
        $this->assertEquals($user2, $message->getUserIfExists());

        $user2->delete();
        $this->assertNull($message->getUserIfExists());
        $this->_checkConsistency();
    }

    public function testCreate() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        /** @var SK_Entity_Conversation $conversation */
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2)));
        $this->_checkConsistency();
        $conversation->setRead($user1);
        $conversation->setRead($user2);
        SKTest_TH::timeForward(100);
        $this->assertEquals(0, $user1->getConversationsUnread()->getCount());
        $this->assertEquals(0, $user1->getConversationsReceived()->getCount());
        $this->assertEquals(0, $user2->getConversationsUnread()->getCount());
        $this->assertEquals(0, $user2->getConversationsReceived()->getCount());
        $this->assertEquals(0, $conversation->getMessages()->getCount());
        /** @var SK_Entity_ConversationMessage_Text $conversationMessage */
        $conversationMessage = SK_Entity_ConversationMessage_Text::createStatic(array(
            'conversation' => $conversation,
            'user'         => $user1,
            'text'         => 'test',
        ));
        $this->_checkConsistency();
        $this->assertEquals($user1, $conversationMessage->getUser());
        $this->assertSameTime(time(), $conversationMessage->getCreated());
        $this->assertRow('sk_conversationMessage', array('id' => $conversationMessage->getId(), 'text' => 'test'));
        $this->assertEquals(1, $conversation->getMessages()->getCount());
        $this->assertEquals(0, $user1->getConversationsReceived()->getCount());
        $this->assertEquals(1, $user2->getConversationsReceived()->getCount());
        $this->assertEquals(1, $user1->getConversationsSent()->getCount());
        $this->assertEquals(0, $user2->getConversationsSent()->getCount());
        $this->assertEquals(0, $user1->getConversationsUnread()->getCount());
        $this->assertEquals(1, $user2->getConversationsUnread()->getCount());
        SK_Entity_ConversationMessage_Text::createStatic(array(
            'conversation' => $conversation,
            'user'         => $user2,
            'text'         => 'test',
        ));
        $this->_checkConsistency();
        $this->assertEquals(1, $user1->getConversationsReceived()->getCount());
        $this->assertEquals(1, $user2->getConversationsSent()->getCount());
        $this->assertEquals(1, $user1->getConversationsUnread()->getCount());
        $this->assertEquals(0, $user2->getConversationsUnread()->getCount());

        try {
            SK_Entity_ConversationMessage_Text::createStatic(array(
                'conversation' => $conversation,
                'user'         => SKTest_TH::createUser(),
                'text'         => 'illegalUser',
            ));
            $this->fail('User can create messages in conversation he isn\'t a part of.');
        } catch (CM_Exception_NotAllowed $ex) {
            $this->assertTrue(true);
        }
        $this->_checkConsistency();
        $this->assertNotRow('sk_conversationMessage', array('text' => 'illegalUser'));
    }
}
