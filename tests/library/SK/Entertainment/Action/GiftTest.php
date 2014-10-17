<?php

class SK_Entertainment_Action_GiftTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testExecute() {
        CM_Db_Db::insert('sk_gift_template', array('tpl_id' => 1, 'active' => 1));
        $sourceUser = SKTest_TH::createUser();
        $targetUser = SKTest_TH::createUser();
        $action = new SK_Entertainment_Action_Gift($targetUser, new SK_Params(array('sourceUser' => $sourceUser)), 100);
        $this->assertTrue($targetUser->getConversations()->isEmpty());
        $this->assertTrue($sourceUser->getConversations()->isEmpty());

        $action->execute();

        $this->assertSame(1, $sourceUser->getConversations()->getCount());
        $this->assertSame(1, $sourceUser->getConversations()->getCount());

        /** @var SK_Entity_Conversation $conversation */
        $conversation = $sourceUser->getConversations()->getItem(0);
        $this->assertSame(1, $conversation->getMessages()->getCount());
        /** @var SK_Entity_ConversationMessage_Gift $conversationMessage */
        $conversationMessage = $conversation->getMessages()->getItem(0);
        $this->assertInstanceOf('SK_Entity_ConversationMessage_Gift', $conversationMessage);
        $this->assertContains(array('id' => $conversationMessage->getGiftId()), new SK_Paging_Gift());
    }

    public function testExecuteSameUser() {
        $user = SKTest_TH::createUser();
        $action = new SK_Entertainment_Action_Gift($user, new SK_Params(array('sourceUser' => $user)), 100);
        $this->assertTrue($user->getConversations()->isEmpty());

        $action->execute();

        $this->assertTrue($user->getConversations()->isEmpty());
    }

    public function testExecuteBlockedUser() {
        $sourceUser = SKTest_TH::createUser();
        $targetUser = SKTest_TH::createUser();
        $targetUser->getBlockings()->add($sourceUser);
        $action = new SK_Entertainment_Action_Gift($targetUser, new SK_Params(array('sourceUser' => $sourceUser)), 100);
        $this->assertTrue($targetUser->getConversations()->isEmpty());
        $this->assertTrue($sourceUser->getConversations()->isEmpty());

        $action->execute();

        $this->assertTrue($targetUser->getConversations()->isEmpty());
        $this->assertTrue($sourceUser->getConversations()->isEmpty());
    }
}
