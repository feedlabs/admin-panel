<?php

class SK_Entity_ConversationTest extends SKTest_TestCase {

    public function testSetRead() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2)));
        $conversation->setRead($user1);
        $this->assertRow('sk_conversationRecipient', array('conversationId' => $conversation->getId(), 'userId' => $user1->getId(), 'read' => 1));
    }

    public function testSetUnread() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2)));
        $this->_checkConsistency();
        $conversation->setRead($user1);
        $conversation->setRead($user2);
        $user1->getConversations()->remove($conversation);
        $this->_checkConsistency();
        $this->assertRow('sk_conversationRecipient', array('conversationId' => $conversation->getId(), 'userId' => $user1->getId(), 'deleted' => 1,
                                                           'read'           => 1));
        $this->assertRow('sk_conversationRecipient', array('conversationId' => $conversation->getId(), 'userId' => $user2->getId(), 'deleted' => 0,
                                                           'read'           => 1));
        $conversation->setUnread();
        $this->_checkConsistency();
        $this->assertRow('sk_conversationRecipient', array('conversationId' => $conversation->getId(), 'userId' => $user1->getId(), 'deleted' => 0,
                                                           'read'           => 0));
        $this->assertRow('sk_conversationRecipient', array('conversationId' => $conversation->getId(), 'userId' => $user2->getId(), 'deleted' => 0,
                                                           'read'           => 0));
    }

    protected function _checkConsistency() {
        if (!empty(CM_Config::get()->duplicateConversationsInMongoDB)) {
            $conversationsCli = new SK_Conversations_Cli();
            $this->assertTrue($conversationsCli->check());
        }
    }

    public function testGetUsers() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $user3 = SKTest_TH::createUser();
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2, $user3)));
        $participants = $conversation->getUsers()->getItems();
        $this->assertEquals($user1, $participants[0]);
        $this->assertEquals($user2, $participants[1]);
        $this->assertEquals($user3, $participants[2]);
    }

    public function testGetMessages() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        /** @var SK_Entity_Conversation $conversation */
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2)));
        $this->_checkConsistency();

        $message = SK_Entity_ConversationMessage_Text::createStatic(array('conversation' => $conversation, 'user' => $user1, 'text' => 'hallo'));
        $this->_checkConsistency();
        SKTest_TH::timeForward(1);
        $gift = SK_Entity_ConversationMessage_Gift::createStatic(array('conversation' => $conversation, 'user' => $user2, 'text' => 1));
        $this->_checkConsistency();

        $messages = $conversation->getMessages()->getItems();
        $this->assertEquals($gift, $messages[0]);
        $this->assertEquals($message, $messages[1]);
    }

    public function testCreateDelete() {
        // test create
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        $this->assertEquals(0, $user1->getConversations()->getCount());
        $this->assertEquals(0, $user2->getConversations()->getCount());
        $this->assertEquals(0, $user1->getConversationsUnread()->getCount());
        $this->assertEquals(0, $user2->getConversationsUnread()->getCount());

        /** @var SK_Entity_Conversation $conversation */
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2)));
        $this->_checkConsistency();
        $this->assertRow('sk_conversation', array('id' => $conversation->getId()));
        $this->assertRow('sk_conversationRecipient', array('conversationId' => $conversation->getId(), 'userId' => $user1->getId()));
        $this->assertRow('sk_conversationRecipient', array('conversationId' => $conversation->getId(), 'userId' => $user2->getId()));
        $this->assertEquals(1, $user1->getConversations()->getCount());
        $this->assertEquals(1, $user2->getConversations()->getCount());
        $this->assertEquals(1, $user1->getConversationsUnread()->getCount());
        $this->assertEquals(1, $user2->getConversationsUnread()->getCount());
        $this->assertEquals($user1->getId(), $conversation->_get('userId'));
        $this->assertSameTime(time(), $conversation->_get('createStamp'));

        SK_Entity_ConversationMessage_Text::createStatic(array('conversation' => $conversation, 'user' => $user1, 'text' => 'foo'));
        $this->_checkConsistency();
        $this->assertEquals(1, $conversation->getMessages()->getCount());

        // test delete
        $conversation->delete();
        $this->_checkConsistency();
        $this->assertEquals(0, $conversation->getMessages()->getCount());
        try {
            new SK_Entity_Conversation($conversation->getId());
            $this->fail('Conversation not deleted.');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->_checkConsistency();
            $this->assertTrue(true);
        }
        $this->assertNotRow('sk_conversation', array('id' => $conversation->getId()));
        $this->assertNotRow('sk_conversationRecipient', array('conversationId' => $conversation->getId()));
        $this->assertNotRow('sk_conversationRecipient', array('conversationId' => $conversation->getId()));
        $this->assertEquals(0, $user1->getConversationsUnread()->getCount());
        $this->assertEquals(0, $user2->getConversationsUnread()->getCount());
        $this->assertEquals(0, $user1->getConversationsUnread()->getCount());
        $this->assertEquals(0, $user2->getConversationsUnread()->getCount());
    }

    public function testDeleteWithReports() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        /** @var SK_Entity_Conversation $conversation */
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2)));
        /** @var SK_Entity_ConversationMessage_Text $message */
        $message = SK_Entity_ConversationMessage_Text::createStatic(array('conversation' => $conversation, 'user' => $user1, 'text' => 'hallo'));
        $this->_checkConsistency();

        $message->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $user2, 'test report');
        $this->assertSame(1, $user1->getReportList()->getCount());

        $user1->getConversations()->remove($conversation);
        $this->assertSame(1, $user1->getReportList()->getCount());

        $user2->getConversations()->remove($conversation);
        $this->assertSame(0, $user1->getReportList()->getCount());
        $this->_checkConsistency();
    }

    public function testSetBlockedForRecipient() {
        $user1 = SKTest_TH::createUser();
        $user2 = SKTest_TH::createUser();
        /** @var SK_Entity_Conversation $conversation */
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $user1, 'recipients' => array($user2)));
        /** @var SK_Entity_ConversationMessage_Text $message */
        $message = SK_Entity_ConversationMessage_Text::createStatic(array('conversation' => $conversation, 'user' => $user1, 'text' => 'hallo'));
        $this->_checkConsistency();
        $this->assertSame(0, $user1->getConversationsSpam()->getCount());
        $this->assertSame(0, $user2->getConversationsSpam()->getCount());
        $this->assertFalse($conversation->getBlockedForRecipient($user1));
        $this->assertFalse($conversation->getBlockedForRecipient($user2));
        $conversation->setBlockedForRecipient($user2, true);
        $this->_checkConsistency();
        $this->assertSame(0, $user1->getConversationsSpam()->getCount());
        $this->assertSame(1, $user2->getConversationsSpam()->getCount());
        $this->assertFalse($conversation->getBlockedForRecipient($user1));
        $this->assertTrue($conversation->getBlockedForRecipient($user2));
        $conversation->setBlockedForRecipient($user2, false);
        $this->_checkConsistency();
        $this->assertSame(0, $user1->getConversationsSpam()->getCount());
        $this->assertSame(0, $user2->getConversationsSpam()->getCount());
        $this->assertFalse($conversation->getBlockedForRecipient($user1));
        $this->assertFalse($conversation->getBlockedForRecipient($user2));
    }
}
