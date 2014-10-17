<?php

class SK_Component_ServiceBundleViewTest extends SKTest_TestCase {

    public function testRender() {
        $serviceBundle = SKTest_TH::createServiceBundle();
        $cmp = new SK_Component_ServiceBundleView(array('serviceBundle' => $serviceBundle));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.plan'));
    }
}
