<?php

class SK_Component_AccountTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_Account();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_Account(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContainsAll(array('Email', 'Username', 'Password', 'Birthday', 'Location'), $page->find('.box:first')->getText());
    }

    public function testPremiumuser() {
        $viewer = $this->_createViewer(SK_Role::PREMIUMUSER);
        $cmp = new SK_Component_Account(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContainsAll(array('Email', 'Username', 'Password', 'Birthday', 'Location'), $page->find('.box:first')->getText());
    }
}
