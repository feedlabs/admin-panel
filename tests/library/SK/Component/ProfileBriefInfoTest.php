<?php

class SK_Component_ProfileBriefInfoTest extends SKTest_TestCase {

    public function testGuest() {
        $params = array('user' => SKTest_TH::createUser());
        $cmp = new SK_Component_ProfileBriefInfo($params);
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.introduction'));
        $this->assertFalse($page->has('.verifyProfile'));
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $user = SKTest_TH::createUser();
        $cmp = new SK_Component_ProfileBriefInfo(array('user' => $user));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_UserInteraction'));
        $this->assertFalse($page->has('.verifyProfile'));
    }

    public function testUserEqualToViewer() {
        $user = SKTest_TH::createUser();
        $viewer = $user;
        $cmp = new SK_Component_ProfileBriefInfo(array('user' => $user));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_UserInteraction'));
        $this->assertTrue($page->has('.verifyProfile'));
    }
}
