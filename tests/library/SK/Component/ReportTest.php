<?php

class SK_Component_ReportTest extends SKTest_TestCase {

    public function testGuest() {
        $params = array('entity' => SKTest_TH::createVideo());

        $this->assertComponentNotAccessible(new SK_Component_Report($params));
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_Report(array('entity' => SKTest_TH::createVideo()));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertContains('Reason', $page->find('label')->getText());
    }
}
