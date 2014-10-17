<?php

class SK_Component_EntityInteractionTest extends SKTest_TestCase {

    public function testGuest() {
        $photo = SKTest_TH::createPhoto();
        $cmp = new SK_Component_EntityInteraction(array('entity' => $photo));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertFalse($page->has('.SK_Component_Pinboard_DropdownList'));
        $this->assertTrue($page->has('.SK_Component_Rating'));
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $photo = SKTest_TH::createPhoto();
        $cmp = new SK_Component_EntityInteraction(array('entity' => $photo));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_Pinboard_DropdownList'));
        $this->assertTrue($page->has('.SK_Component_Rating'));
    }
}
