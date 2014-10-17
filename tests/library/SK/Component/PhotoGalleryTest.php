<?php

class SK_Component_PhotoGalleryTest extends SKTest_TestCase {

    public function testGuest() {
        $photo = SKTest_TH::createPhoto();
        $cmp = new SK_Component_PhotoGallery(array('photo' => $photo));

        try {
            $this->_renderComponent($cmp);
            $this->fail('Child cmp SK_Component_PhotoView should not be accessible');
        } catch (CM_Exception_AuthRequired $e) {
            $this->assertTrue(true);
        }
    }

    public function testUser() {
        $viewer = $this->_createViewer();
        $photo = SKTest_TH::createPhoto();
        $photo->setDescription('blah-bla');
        $cmp = new SK_Component_PhotoGallery(array('photo' => $photo));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_PhotoView'));
    }

    public function testPinboard() {
        $viewer = $this->_createViewer();
        $photo1 = SKTest_TH::createPhoto();
        $photo2 = SKTest_TH::createPhoto();

        $pinboard = SKTest_TH::createPinboard();
        $pinboard->add($photo1);
        $pinboard->add($photo2);

        $cmp = new SK_Component_PhotoGallery(array('photo' => $photo1, 'entityList' => $pinboard->getPinList()));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('li[data-photo-id="' . $photo1->getId() . '"]'));
        $this->assertTrue($page->has('li[data-photo-id="' . $photo2->getId() . '"]'));
    }

    /**
     * @expectedException CM_Exception_Nonexistent
     * @expectedExceptionMessage Cannot find photo
     */
    public function testPinboardWithNotContainedPhoto() {
        $viewer = $this->_createViewer();
        $photo1 = SKTest_TH::createPhoto();
        $photo2 = SKTest_TH::createPhoto();

        $pinboard = SKTest_TH::createPinboard();
        $pinboard->add($photo2);

        $cmp = new SK_Component_PhotoGallery(array('photo' => $photo1, 'entityList' => $pinboard->getPinList()));
        $this->_renderComponent($cmp, $viewer);
    }
}
