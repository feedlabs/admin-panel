<?php

class SK_Component_MemberFeedbackTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_MemberFeedback();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertSame(null, $page->find('[name="member_email"]')->getAttribute('value'));
        $this->assertContains('Send', $page->find('button .label')->getText());
    }

    public function testEmailVerified() {
        $viewer = $this->_createViewer();
        $viewer->setEmailVerified(true);
        $cmp = new SK_Component_MemberFeedback(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertSame($viewer->getEmail(), $page->find('[name="member_email"]')->getAttribute('value'));
    }

    public function testEmailUnverified() {
        $viewer = $this->_createViewer();
        $viewer->setEmailVerified(false);
        $cmp = new SK_Component_MemberFeedback(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertSame(null, $page->find('[name="member_email"]')->getAttribute('value'));
    }

    public function testCaptchaRendering() {
        $captcha = CM_Captcha::create();
        $image = $captcha->render(200, 40);
        $this->assertTrue(0 === strpos($image, "\x89PNG\r\n\x1A\n"));
    }
}
