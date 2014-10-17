<?php

class SK_Component_MailboxTest extends SKTest_TestCase {

    public function testGuest() {
        $this->assertComponentNotAccessible(new SK_Component_Mailbox());
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();

        // received list
        $cmp = new SK_Component_Mailbox(array('mailbox' => 'inbox'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('No Conversations', $page->find('.noContent')->getText());

        // sent list
        $cmp = new SK_Component_Mailbox(array('mailbox' => 'sent'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('No Conversations', $page->find('.noContent')->getText());

        // all list
        $cmp = new SK_Component_Mailbox(array('mailbox' => 'all'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('No Conversations', $page->find('.noContent')->getText());

        // unread list
        $cmp = new SK_Component_Mailbox(array('mailbox' => 'unread'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('No Conversations', $page->find('.noContent')->getText());
    }

    public function testNewMessage() {
        $viewer = $this->_createViewer();

        $cmp = new SK_Component_Mailbox(array('mailbox' => 'inbox', 'conversation' => null));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertTrue($page->has('.SK_Component_Conversation .formField.recipients'));
        $this->assertTrue($page->has('.SK_Component_Conversation .writeMessage'));
    }

    public function testConversation() {
        $viewer = $this->_createViewer();
        $conversation = SKTest_TH::createConversation($viewer);

        $cmp = new SK_Component_Mailbox(array('mailbox' => 'inbox', 'conversation' => $conversation));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertTrue($page->has('.SK_Component_Conversation .participants'));
        $this->assertTrue($page->has('.SK_Component_Conversation .writeMessage'));
    }
}
