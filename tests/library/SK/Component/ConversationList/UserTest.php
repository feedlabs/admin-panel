<?php

class SK_Component_ConversationList_UserTest extends SKTest_TestCase {

    public function testGuest() {
        $this->assertComponentNotAccessible(new SK_Component_ConversationList_User());
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();

        //with content
        $existingUser = SKTest_TH::createUser();
        $conversation = SKTest_TH::createConversation($viewer, $existingUser);
        $cmp = new SK_Component_ConversationList_User(array('mailbox' => 'sent'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.conversations .conversation-content .username'));
        $conversation->delete();

        //deleted member
        $nonExististentUser = SKTest_TH::createUser();
        $conversation = SKTest_TH::createConversation($nonExististentUser, $viewer);
        $nonExististentUser->delete();
        $cmp = new SK_Component_ConversationList_User(array('mailbox' => 'inbox'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Deleted Member', $page->find('.conversations .conversation-content .username')->getText());
        $conversation->delete();
    }

    public function testAdminUser() {
        $viewer = $this->_createViewer(SK_Role::ADMIN);

        $owner = SKTest_TH::createUser();
        SKTest_TH::createConversation($owner, SKTest_TH::createUser());
        $cmp = new SK_Component_ConversationList_User(array('mailbox' => 'all', 'user' => $owner));

        $this->assertComponentAccessible($cmp, $viewer);
        $this->_renderComponent($cmp, $viewer);
    }

    public function testOtherUser() {
        $viewer = $this->_createViewer();

        $owner = SKTest_TH::createUser();
        SKTest_TH::createConversation($owner, SKTest_TH::createUser());
        $cmp = new SK_Component_ConversationList_User(array('mailbox' => 'all', 'user' => $owner), $viewer);

        $this->assertComponentNotAccessible($cmp);
    }

    public function testShowBlocked() {
        $owner = SKTest_TH::createUser();
        $viewer = $owner;
        $conversation = SKTest_TH::createConversation($owner, SKTest_TH::createUser());
        $conversation->setBlocked(true);

        $cmp = new SK_Component_ConversationList_User(array('mailbox' => 'all', 'user' => $owner, 'showBlocked' => false));
        $html = $this->_renderComponent($cmp, $viewer);
        $this->assertSame(0, $html->find('.conversations > .conversation')->count());

        $cmp = new SK_Component_ConversationList_User(array('mailbox' => 'all', 'user' => $owner, 'showBlocked' => true));
        $html = $this->_renderComponent($cmp, $viewer);
        $this->assertSame(1, $html->find('.conversations > .conversation')->count());
    }
}
