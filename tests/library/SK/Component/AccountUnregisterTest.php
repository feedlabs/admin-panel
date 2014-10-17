<?php

class SK_Component_AccountUnregisterTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_AccountUnregister();
        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_AccountUnregister(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Delete My Account', $page->find('.unregister_profile button')->getText());
    }

    public function testPremiumuser() {
        $viewer = $this->_createViewer(SK_Role::PREMIUMUSER);
        $cmp = new SK_Component_AccountUnregister(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Delete My Account', $page->find('.unregister_profile button')->getText());
    }

    public function testCaptchaRendering() {
        $captcha = CM_Captcha::create();
        $image = $captcha->render(200, 40);
        $this->assertTrue(0 === strpos($image, "\x89PNG\r\n\x1A\n"));
    }
}
