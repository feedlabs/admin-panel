<?php

class SK_EntityProvider_AbstractTest extends SKTest_TestCase {

    public function setUp() {
        CM_Config::get()->CM_Model_Abstract->types[99] = 'SK_EntityProvider_Mock';
        CM_Config::get()->SK_EntityProvider_Abstract->processAllEnable = true;
        SK_EntityProvider_Mock::createStatic(array('name' => 'testProvider1', 'processInterval' => 500));
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function  testConstruct() {
        $entityProviderMock = new SK_EntityProvider_Mock();
        $this->assertSame('testProvider1', $entityProviderMock->getName());
        $this->assertSame(500, $entityProviderMock->getProcessInterval());
    }

    public function testProcess() {
        /** @var $entityProvider SK_EntityProvider_Abstract */
        $entityProvider = $this->getMockForAbstractClass('SK_EntityProvider_Mock', array(), '', true, true, true, array('_process',
            'factory'));
        $entityProvider->expects($this->exactly(2))->method('_process');

        $this->assertSame(null, $entityProvider->getProcessLastStamp());
        $entityProvider->process();
        $processLastStamp = $entityProvider->getProcessLastStamp();
        $this->assertSameTime(time(), $entityProvider->getProcessLastStamp());
        $this->assertNotNull($processLastStamp);
        $entityProvider->process();
        SKTest_TH::timeForward(800);
        $entityProvider->process();
        $this->assertSameTime(time(), $entityProvider->getProcessLastStamp());
    }

    public function testProcessAll() {
        SKTest_TH::timeForward(600);
        SK_EntityProvider_Abstract::processAll();
        $entityProvider = new SK_EntityProvider_Mock();
        $processLastStamp = $entityProvider->getProcessLastStamp();
        $this->assertSameTime(time(), $processLastStamp);

        CM_Config::get()->SK_EntityProvider_Abstract->processAllEnable = false;
        SK_EntityProvider_Abstract::processAll();
        $entityProvider = new SK_EntityProvider_Mock();
        $this->assertSameTime($processLastStamp, $entityProvider->getProcessLastStamp());
    }
}

class SK_EntityProvider_Mock extends SK_EntityProvider_Abstract {

    public static function getTypeStatic() {
        return 99;
    }

    protected function _process() {
    }
}
