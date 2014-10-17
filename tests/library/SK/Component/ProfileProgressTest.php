<?php

class SK_Component_ProfileProgressTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_ProfileProgress();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_ProfileProgress(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('your email address', $page->find('.stepList')->getText());
        $this->assertContains('Upload photos', $page->find('.stepList')->getText());
        $this->assertContains('Upgrade to Premium', $page->find('.stepList')->getText());
        $this->assertContains('your profile', $page->find('.stepList')->getText());
    }

    public function testFreeuserEmailVerified() {
        $viewer = $this->_createViewer();
        $viewer->setEmailVerified(true);
        $cmp = new SK_Component_ProfileProgress(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertNotContains('your email address', $page->find('.stepList')->getText());
        $this->assertContains('Upload photos', $page->find('.stepList')->getText());
        $this->assertContains('Upgrade to Premium', $page->find('.stepList')->getText());
        $this->assertContains('your profile', $page->find('.stepList')->getText());
    }

    public function testFreeuserPhotoApproved() {
        $viewer = $this->_createViewer();
        $photo = SKTest_TH::createPhoto($viewer);
        $photo->getVerification()->setApproved();
        $cmp = new SK_Component_ProfileProgress(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('your email address', $page->find('.stepList')->getText());
        $this->assertNotContains('Upload photos', $page->find('.stepList')->getText());
        $this->assertContains('Upgrade to Premium', $page->find('.stepList')->getText());
        $this->assertNotContains('your profile', $page->find('.stepList')->getText());
    }

    public function testPremiumuser() {
        $viewer = $this->_createViewer(SK_Role::PREMIUMUSER);
        $cmp = new SK_Component_ProfileProgress(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('your email address', $page->find('.stepList')->getText());
        $this->assertContains('Upload photos', $page->find('.stepList')->getText());
        $this->assertNotContains('Upgrade to Premium', $page->find('.stepList')->getText());
        $this->assertContains('your profile', $page->find('.stepList')->getText());
    }

    public function testPremiumuserEmailVerified() {
        $viewer = $this->_createViewer(SK_Role::PREMIUMUSER);
        $viewer->setEmailVerified(true);
        $cmp = new SK_Component_ProfileProgress(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertNotContains('your email address', $page->find('.stepList')->getText());
        $this->assertContains('Upload photos', $page->find('.stepList')->getText());
        $this->assertNotContains('Upgrade to Premium', $page->find('.stepList')->getText());
        $this->assertContains('your profile', $page->find('.stepList')->getText());
    }

    public function testPremiumuserPhotoApproved() {
        $viewer = $this->_createViewer(SK_Role::PREMIUMUSER);
        $photo = SKTest_TH::createPhoto($viewer);
        $photo->getVerification()->setApproved();
        $cmp = new SK_Component_ProfileProgress(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('your email address', $page->find('.stepList')->getText());
        $this->assertNotContains('Upload photos', $page->find('.stepList')->getText());
        $this->assertNotContains('Upgrade to Premium', $page->find('.stepList')->getText());
        $this->assertNotContains('your profile', $page->find('.stepList')->getText());
    }
}
