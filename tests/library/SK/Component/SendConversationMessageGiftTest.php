<?php

class SK_Component_SendConversationMessageGiftTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_SendConversationMessageGift();
        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        CM_Db_Db::insert('sk_gift_template', array('active' => 1));

        $cmp = new SK_Component_SendConversationMessageGift(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Send', $page->find('button')->getAttribute('value'));
        $this->assertContains('giftId', $page->find('input')->getAttribute('name'));
    }
}
