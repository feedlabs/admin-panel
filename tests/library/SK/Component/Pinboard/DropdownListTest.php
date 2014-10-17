<?php

class SK_Component_Pinboard_DropdownListTest extends SKTest_TestCase {

    public function testGuest() {
        $photo = SKTest_TH::createPhoto();
        $cmp = new SK_Component_Pinboard_DropdownList(array('entity' => $photo));

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $photo = SKTest_TH::createPhoto();

        $cmp = new SK_Component_Pinboard_DropdownList(array('entity' => $photo));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertSame(1, $page->find('.pinItem.pinItemDefault.setPinned.visible')->count());

        SKTest_TH::createPinboard($viewer, 'pinboard one');
        SKTest_TH::createPinboard($viewer, 'pinboard two');
        $cmp = new SK_Component_Pinboard_DropdownList(array('entity' => $photo));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertSame(1, $page->find('.pinItem.pinItemDefault.setPinned')->count());
        $this->assertSame(3, $page->find('.pinItem')->count());
        $this->assertContains('pinboard one', $page->getText());
        $this->assertContains('pinboard two', $page->getText());
    }
}
