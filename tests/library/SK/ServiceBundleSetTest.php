<?php

class SK_ServiceBundleSetTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        /** @var SK_ServiceBundleSet $set */
        $set = SK_ServiceBundleSet::createStatic();
        $this->assertInstanceOf('SK_ServiceBundleSet', $set);
        $this->assertFalse($set->getEnabled());
    }

    public function testDelete() {
        $set = SKTest_TH::createServiceBundleSet();
        $set->getServiceBundles()->add(SKTest_TH::createServiceBundle());
        $this->assertFalse($set->getServiceBundles()->isEmpty());

        $set->delete();
        try {
            SKTest_TH::reinstantiateModel($set);
            $this->fail('ServiceBundleSet not deleted.');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
        $this->assertTrue($set->getServiceBundles()->isEmpty());
    }

    public function testGetSetEnabled() {
        $set = SKTest_TH::createServiceBundleSet();
        $this->assertFalse($set->getEnabled());
        $set->setEnabled();
        $this->assertTrue($set->getEnabled());
        $set->setEnabled(false);
        $this->assertFalse($set->getEnabled());
        $set->setEnabled(true);
        $this->assertTrue($set->getEnabled());
    }
}
