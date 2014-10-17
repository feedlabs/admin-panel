<?php

class SK_Component_ChangePasswordTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_ChangePassword();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_ChangePassword(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Change Password', $page->find('.box-header h2')->getText());
        $this->assertContains('Old Password', $page->find('.change_password')->getText());
        $this->assertContains('New Password', $page->find('.change_password')->getText());
        $this->assertContains('Confirm Password', $page->find('.change_password')->getText());
    }
}
