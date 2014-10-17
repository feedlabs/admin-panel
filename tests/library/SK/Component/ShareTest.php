<?php

class SK_Component_ShareTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_Share();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_Share();
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.share-photo'));

        $this->assertContains('Title', $page->find('.share-blogpost label')->getText());
        $this->assertContains('Text', $page->find('.share-blogpost .formField:eq(1) label')->getText());
        $this->assertTrue($page->has('.share-blogpost select[name="privacy"]'));
        $this->assertContains('Tags', $page->find('.share-blogpost .formField:eq(2) label')->getText());

        $this->assertContains('Title', $page->find('.share-video form')->getText());
        $this->assertContains('Code', $page->find('.share-video form')->getText());
        $this->assertContains('Description', $page->find('.share-video form')->getText());
        $this->assertTrue($page->has('.share-video select[name="privacy"]'));
        $this->assertContains('Tags', $page->find('.share-video form')->getText());
        $this->assertContains('.internals.asset.privacy.1', $page->find('.share-video select[name="privacy"] option:eq(0)')->getHtml());
        $this->assertContains('.internals.asset.privacy.2', $page->find('.share-video select[name="privacy"] option:eq(1)')->getHtml());
    }
}
