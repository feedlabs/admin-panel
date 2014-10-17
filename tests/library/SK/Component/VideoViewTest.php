<?php

class SK_Component_VideoViewTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_VideoView(array('video' => SKTest_TH::createVideo()));

        $this->assertComponentNotAccessible($cmp);
    }

    public function testGuestPremiumVideo() {
        $cmp = new SK_Component_VideoView(array('video' => SKTest_TH::createVideoPremium()));

        $this->assertComponentAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_VideoView(array('video' => SKTest_TH::createVideo($viewer)));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContainsAll(array('Title', 'Description'), $page->find('form')->getText());
        $this->assertNotContains('Tags', $page->find('form')->getText());
        $this->assertTrue($page->has('.formAction select[name="privacy"]'));
        $this->assertContains('.internals.asset.privacy.1', $page->find('.formAction select[name="privacy"] option:eq(0)')->getHtml());
        $this->assertContains('.internals.asset.privacy.2', $page->find('.formAction select[name="privacy"] option:eq(1)')->getHtml());
        $this->assertTrue($page->has('.videoView .embeddedWrapper'));
        $this->assertContains($viewer->getDisplayName(), $page->find('.user-thumb img')->getAttribute('title'));
    }

    public function testFreeuserInvalidVideo() {
        $viewer = $this->_createViewer();

        $cmp = new SK_Component_VideoView(array('video' => 81289736));
        $this->assertComponentNotRenderable($cmp, $viewer);

        $cmp = new SK_Component_VideoView(array('video' => null));
        $this->assertComponentNotRenderable($cmp, $viewer);
    }
}
