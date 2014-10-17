<?php

class SK_Component_ConversationList_MailboxTest extends SKTest_TestCase {

    public function testGuest() {
        $this->assertComponentNotAccessible(new SK_Component_ConversationList_Mailbox());
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();

        //with content
        $existingUser = SKTest_TH::createUser();
        $conversation = SKTest_TH::createConversation($viewer, $existingUser);
        $cmp = new SK_Component_ConversationList_Mailbox(array('mailbox' => 'sent'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.conversations .conversation-content .username'));
        $conversation->delete();

        //deleted member
        $nonExististentUser = SKTest_TH::createUser();
        $conversation = SKTest_TH::createConversation($nonExististentUser, $viewer);
        $nonExististentUser->delete();
        $cmp = new SK_Component_ConversationList_Mailbox(array('mailbox' => 'inbox'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Deleted Member', $page->find('.conversations .conversation-content .username')->getText());
        $conversation->delete();
    }

    public function testAdminUser() {
        $viewer = $this->_createViewer(SK_Role::ADMIN);

        $owner = SKTest_TH::createUser();
        SKTest_TH::createConversation($owner, SKTest_TH::createUser());
        $cmp = new SK_Component_ConversationList_Mailbox(array('mailbox' => 'all', 'user' => $owner));

        $this->assertComponentAccessible($cmp, $viewer);
        $this->_renderComponent($cmp, $viewer);
    }

    public function testOtherUser() {
        $viewer = $this->_createViewer();

        $owner = SKTest_TH::createUser();
        SKTest_TH::createConversation($owner, SKTest_TH::createUser());
        $cmp = new SK_Component_ConversationList_Mailbox(array('mailbox' => 'all', 'user' => $owner));

        $this->assertComponentNotAccessible($cmp, $viewer);
    }

    public function testShowBlocked() {
        $owner = SKTest_TH::createUser();
        $viewer = $owner;
        $conversation = SKTest_TH::createConversation($owner, SKTest_TH::createUser());
        $conversation->setBlocked(true);

        $cmp = new SK_Component_ConversationList_Mailbox(array('mailbox' => 'all', 'user' => $owner, 'showBlocked' => false));
        $html = $this->_renderComponent($cmp, $viewer);
        $this->assertSame(0, $html->find('.conversations > .conversation')->count());

        $cmp = new SK_Component_ConversationList_Mailbox(array('mailbox' => 'all', 'user' => $owner, 'showBlocked' => true));
        $html = $this->_renderComponent($cmp, $viewer);
        $this->assertSame(1, $html->find('.conversations > .conversation')->count());
    }

    public function testAjax_setAllRead() {
        $sender = SKTest_TH::createUser();
        $recipient = SKTest_TH::createUser();
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $sender, 'recipients' => array($recipient)));
        $conversationMessage = SK_Entity_ConversationMessage_Text::createStatic(array(
            'conversation' => $conversation,
            'user'         => $sender,
            'text'         => 'foo',
        ));
        $component = new SK_Component_ConversationList_Mailbox(['mailbox' => 'unread', 'user' => $recipient]);

        $this->assertFalse($conversation->getReadForRecipient($recipient));

        $environment = new CM_Frontend_Environment(null, $recipient);
        $this->getResponseAjax($component, 'setAllRead', [], $environment);

        CMTest_TH::reinstantiateModel($conversation);
        $this->assertTrue($conversation->getReadForRecipient($recipient));
    }

    public function testAjax_setAllReadEmptyConversation() {
        $sender = SKTest_TH::createUser();
        $recipient = SKTest_TH::createUser();
        $conversation = SK_Entity_Conversation::createStatic(array('user' => $sender, 'recipients' => array($recipient)));
        $component = new SK_Component_ConversationList_Mailbox(['mailbox' => 'unread', 'user' => $recipient]);

        $this->assertFalse($conversation->getReadForRecipient($recipient));

        $environment = new CM_Frontend_Environment(null, $recipient);
        $this->getResponseAjax($component, 'setAllRead', [], $environment);

        CMTest_TH::reinstantiateModel($conversation);
        $this->assertFalse($conversation->getReadForRecipient($recipient));
    }
}
