<?php

class SK_ServiceTest extends SKTest_TestCase {

    public function testFactory() {
        $service = SK_Service_Abstract::factory(SK_Service_Abstract::COIN, 123);
        $this->assertEquals(SK_Service_Abstract::COIN, $service->getId());
        $this->assertEquals(123, $service->getAmount());
    }
}
