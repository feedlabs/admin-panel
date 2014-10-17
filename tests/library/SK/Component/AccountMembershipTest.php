<?php

class SK_Component_AccountMembershipTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_AccountMembership();
        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_AccountMembership();
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Upgrade to .internals.role.2', $page->getText());
    }

    public function testPremiumuser() {
        $viewer = $this->_createViewer(SK_Role::PREMIUMUSER);
        $cmp = new SK_Component_AccountMembership(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('.internals.role.' . SK_Role::PREMIUMUSER, $page->getText());
    }
}
