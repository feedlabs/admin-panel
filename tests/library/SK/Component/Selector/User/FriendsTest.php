<?php

class SK_Component_Selector_User_FriendsTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_Selector_User_Friends();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $friend = SKTest_TH::createUser();
        $viewer->getFriends()->add($friend);
        $cmp = new SK_Component_Selector_User_Friends(null);
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains($friend->getUsername(), $page->getText());
    }

    public function testFreeuserEmpty() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_Selector_User_Friends(null);

        $this->assertComponentNotRenderable($cmp, $viewer, 'CM_Exception_Invalid');
    }
}
