<?php

class SK_Component_DmcaTest extends SKTest_TestCase {

    public function testGuest() {
        $cmp = new SK_Component_Dmca();
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.article'));
    }
}
