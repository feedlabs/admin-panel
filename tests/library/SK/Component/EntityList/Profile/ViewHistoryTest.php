<?php

class SK_Component_EntityList_Profile_WhoViewedMeTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_EntityList_Profile_WhoViewedMe();

        $this->assertComponentNotAccessible($cmp);
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();

        // default/month: within 30 days
        $cmp = new SK_Component_EntityList_Profile_WhoViewedMe(array('period' => 'month'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_EntityList_Profile_WhoViewedMe'));

        // week: within 7 days
        $cmp = new SK_Component_EntityList_Profile_WhoViewedMe(array('period' => 'week'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_EntityList_Profile_WhoViewedMe'));

        // day: within 1 day
        $cmp = new SK_Component_EntityList_Profile_WhoViewedMe(array('period' => 'day'));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.SK_Component_EntityList_Profile_WhoViewedMe'));
    }
}
