<?php

class SK_Component_BlogpostViewTest extends SKTest_TestCase {

    public function testGuest() {
        $blogpost = SKTest_TH::createBlogpost();
        $cmp = new SK_Component_BlogpostView(array('blogpost' => $blogpost));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.blogpostView'));
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $blogpost = SKTest_TH::createBlogpost();
        $cmp = new SK_Component_BlogpostView(array('blogpost' => $blogpost));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.blogpostView'));
    }

    public function testEdit() {
        $viewer = $this->_createViewer();
        $blogpost = SKTest_TH::createBlogpost($viewer);
        $cmp = new SK_Component_BlogpostView(array('blogpost' => $blogpost));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Title', $page->find('.formField:eq(0) label')->getText());
        $this->assertContains('Text', $page->find('.formField:eq(1) label')->getText());
        $this->assertTrue($page->has('.formAction select[name="privacy"]'));
        $this->assertNotContains('Tags', $page->find('form')->getText());
        $this->assertTrue($page->has('button[value="Save"]'));
        $this->assertTrue($page->has('button[value="Delete"]'));
    }
}
