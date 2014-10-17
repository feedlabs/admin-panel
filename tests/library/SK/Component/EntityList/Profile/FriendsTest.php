<?php

class SK_Component_EntityList_Profile_FriendsSent extends SKTest_TestCase {

    public function testGuest() {
        // Without Friends
        $user = SKTest_TH::createUser();
        $cmp = new SK_Component_EntityList_Profile_Friends(array('user' => $user));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertFalse($page->has('.entity'));

        // With Friends
        $user->getFriends()->add(SKTest_TH::createUser());
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.entity'));
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_EntityList_Profile_Friends(array('user' => $viewer));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertFalse($page->has('.entity'));

        // With Friends
        $viewer->getFriends()->add(SKTest_TH::createUser());
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.entity'));
    }
}
