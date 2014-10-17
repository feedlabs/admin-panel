<?php

class SK_Entertainment_Action_ConversationMessageTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testExecute() {
        $messages = array('foo', 'bar');
        $targetUser = SKTest_TH::createUser();
        $targetUser->setOnline(false);
        $sourceUser = SKTest_TH::createUser();
        $sourceUser->getRoles()->add(SK_Role::ENTERTAINER);
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => 'foo'));
        $messageBody = '{' . implode('|', $messages) . '}';
        SKTest_TH::createEntertainmentMessage($messageSet, $messageBody);
        $action = new SK_Entertainment_Action_ConversationMessage($targetUser, new SK_Params(array('sourceUser' => $sourceUser,
                                                                                                   'messageSet' => $messageSet)), time());
        $this->assertTrue($sourceUser->getConversations()->isEmpty());
        $this->assertTrue($targetUser->getConversations()->isEmpty());
        $this->assertFalse($targetUser->getOnline());

        $action->execute();

        $this->assertSame(1, $sourceUser->getConversations()->getCount());
        $this->assertSame(1, $targetUser->getConversations()->getCount());
        /** @var SK_Entity_Conversation $conversation */
        $conversation = $targetUser->getConversations()->getItem(0);
        $this->assertEquals($conversation, $targetUser->getConversations()->getItem(0));
        /** @var SK_Entity_ConversationMessage_Text $conversationMessage */
        $conversationMessage = $conversation->getMessages()->getItem(0);
        $this->assertInstanceOf('SK_Entity_ConversationMessage_Text', $conversationMessage);
        $this->assertContains($conversationMessage->getText(), $messages);
        $this->assertNotEquals($messageBody, $conversationMessage->getText());
    }

    public function testExecuteSameUser() {
        $user = SKTest_TH::createUser();
        $user->getRoles()->add(SK_Role::ENTERTAINER);
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => 'foo'));
        SKTest_TH::createEntertainmentMessage($messageSet, 'foo');
        $action = new SK_Entertainment_Action_ConversationMessage($user, new SK_Params(array('sourceUser' => $user,
                                                                                             'messageSet' => $messageSet)), time());
        $this->assertTrue($user->getConversations()->isEmpty());

        $action->execute();

        $this->assertTrue($user->getConversations()->isEmpty());
    }

    public function testExecuteBlockedUser() {
        $targetUser = SKTest_TH::createUser();
        $sourceUser = SKTest_TH::createUser();
        $sourceUser->getRoles()->add(SK_Role::ENTERTAINER);
        $targetUser->getBlockings()->add($sourceUser);
        $messageSet = SK_Entertainment_MessageSet::createStatic(array('description' => 'foo'));
        SKTest_TH::createEntertainmentMessage($messageSet, 'foo');
        $action = new SK_Entertainment_Action_ConversationMessage($targetUser, new SK_Params(array('sourceUser' => $sourceUser,
                                                                                                   'messageSet' => $messageSet)), time());
        $this->assertTrue($sourceUser->getConversations()->isEmpty());
        $this->assertTrue($targetUser->getConversations()->isEmpty());

        $action->execute();

        $this->assertTrue($sourceUser->getConversations()->isEmpty());
        $this->assertTrue($targetUser->getConversations()->isEmpty());
    }
}
