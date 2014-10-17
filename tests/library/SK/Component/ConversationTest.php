<?php

class SK_Component_ConversationTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
        CM_Db_Db::insert('cm_actionLimit', array('actionType', 'actionVerb', 'type', 'role', 'limit',
            'period'), array(array(SK_Action_Entity_ConversationMessage_Text::getTypeStatic(),
            CM_Action_Abstract::getVerbByVerbName(SK_Action_Abstract::VIEW), SK_ActionLimit_Daily::getTypeStatic(), null, 0, 86400),
            array(SK_Action_Entity_ConversationMessage_Text::getTypeStatic(), CM_Action_Abstract::getVerbByVerbName(SK_Action_Abstract::VIEW),
                SK_ActionLimit_Daily::getTypeStatic(), SK_Role::PREMIUMUSER, 10, 86400),));
    }

    public function testGuest() {
        $conversation = SKTest_TH::createConversation();
        $cmp = new SK_Component_Conversation(array('conversation' => $conversation));

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $sender = SKTest_TH::createUser(SK_User::SEX_FEMALE);
        $conversation = SKTest_TH::createConversation($sender, $viewer);
        $cmp = new SK_Component_Conversation(array('conversation' => $conversation));
        $page = $this->_renderComponent($cmp, $viewer);

        //message is not readable
        $this->assertSame(1, $page->find('.messagesContainer .message')->count());
        $this->assertContains('to read this message', $page->find('.messagesContainer .message')->getText());
        $this->assertTrue($page->has('button .icon-send'));

        //own message is readable
        $message = SK_Entity_ConversationMessage_Text::createStatic(array('conversation' => $conversation, 'user' => $viewer, 'text' => 'hallo'));
        $page = $this->_renderComponent($cmp, $viewer);
        $this->assertSame(2, $page->find('.messagesContainer .message')->count());
        $this->assertContains('hallo', $page->find('.messagesContainer .message[data-conversation-message-id="' . $message->getId() . '"]')->getText());

        //message from deleted member
        $conversation->delete();
        $nonexistentUser = SKTest_TH::createUser();
        $conversation = SKTest_TH::createConversation($nonexistentUser, $viewer);
        $nonexistentUser->delete();
        $cmp = new SK_Component_Conversation(array('conversation' => $conversation));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertContains('Deleted Member', $page->find('.messagesContainer .username')->getText());
        $this->assertContains('to read this message', $page->find('.messagesContainer .message:last')->getText());
        $this->assertTrue($page->has('button .icon-send'));

        $this->assertComponentAccessible($cmp, $viewer);
    }

    public function testPremiumuser() {
        $viewer = $this->_createViewer(SK_Role::PREMIUMUSER);
        $existingUser = SKTest_TH::createUser();
        $conversation = SKTest_TH::createConversation($existingUser, $viewer);
        $cmp = new SK_Component_Conversation(array('conversation' => $conversation));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertContains('some random text blah blah blah!', $page->find('.message')->getText());
        $this->assertTrue($page->has('button .icon-send'));

        //message from deleted member
        $conversation->delete();
        $nonexistentUser = SKTest_TH::createUser();
        $conversation = SKTest_TH::createConversation($nonexistentUser, $viewer);
        $nonexistentUser->delete();
        $cmp = new SK_Component_Conversation(array('conversation' => $conversation));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertContains('Deleted Member', $page->find('.messagesContainer .username')->getText());
        $this->assertContains('some random text blah blah blah!', $page->find('.message')->getText());
        $this->assertTrue($page->has('button .icon-send'));

        $this->assertComponentAccessible($cmp, $viewer);
    }

    public function testAdminUser() {
        $viewer = $this->_createViewer(SK_Role::ADMIN);

        $owner = SKTest_TH::createUser();
        $conversation = SKTest_TH::createConversation($owner, SKTest_TH::createUser());
        $cmp = new SK_Component_Conversation(array('conversation' => $conversation, 'user' => $owner));

        $this->assertComponentAccessible($cmp, $viewer);
        $this->_renderComponent($cmp, $viewer);
    }

    public function testOtherUser() {
        $viewer = $this->_createViewer();

        $owner = SKTest_TH::createUser();
        $conversation = SKTest_TH::createConversation($owner, SKTest_TH::createUser());
        $cmp = new SK_Component_Conversation(array('conversation' => $conversation, 'user' => $owner));

        $this->assertComponentNotAccessible($cmp, $viewer);
    }
}
