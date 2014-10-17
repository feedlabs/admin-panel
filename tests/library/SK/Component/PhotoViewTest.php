<?php

class SK_Component_PhotoViewTest extends SKTest_TestCase {

    public function testGuest() {
        $this->assertComponentNotAccessible(new SK_Component_PhotoView());
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $photo = SKTest_TH::createPhoto($viewer);
        $photo->setDescription('blah-bla');
        $cmp = new SK_Component_PhotoView(array('photo' => $photo));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.user-thumb'));
        $this->assertTrue($page->has('.SK_Component_Comments'));
        $this->assertContains('blah-bla', $page->find('.photoSidebar')->getText());
    }
}
