<?php

class SK_Component_EmailVerifyTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_EmailVerify();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_EmailVerify(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Please check your email account for further instructions!', $page->find('.box-body')->getText());
        $this->assertContains('Resend verification email', $page->find('.box-header h2')->getText());
    }
}
