<?php

class SK_Component_SendConversationMessageTextTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_SendConversationMessageText();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();

        $cmp = new SK_Component_SendConversationMessageText(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertContains('Send', $page->find('button')->getAttribute('value'));
    }
}
