<?php

class SK_Entertainment_ScheduleItemTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
        SKTest_TH::clearEnv();
    }

    public function testCreate() {
        $schedule = SK_Entertainment_Schedule_Offline::createStatic(array('description' => null));
        /** @var SK_Entertainment_ScheduleItem $scheduleItem */
        $scheduleItem = SK_Entertainment_ScheduleItem::createStatic(array('schedule'           => $schedule, 'offset' => 10, 'actionType' => 21,
                                                                          'sourceUserTemplate' => 1));
        $this->assertInstanceOf('SK_Entertainment_ScheduleItem', $scheduleItem);
        $this->assertSame(21, $scheduleItem->getActionType());
        $this->assertSame(10, $scheduleItem->getOffset());
        $this->assertEquals($schedule, $scheduleItem->getSchedule());
        $this->assertSame(1, $scheduleItem->getSourceUserTemplate());
        $this->assertSame(array(), $scheduleItem->getActionConfiguration()->getParamsEncoded());
    }

    public function testDelete() {
        $scheduleItem = SKTest_TH::createEntertainmentScheduleItem();
        $scheduleItem->delete();
        try {
            SKTest_TH::reinstantiateModel($scheduleItem);
            $this->fail("Could reinstantiate deleted scheduleItem.");
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }

    public function testSetActionConfiguration() {
        $scheduleItem = SKTest_TH::createEntertainmentScheduleItem();
        $this->assertSame(array(), $scheduleItem->getActionConfiguration()->getParamsEncoded());

        $scheduleItem->setActionConfiguration(array('foo' => 'bar'));

        $this->assertInstanceOf('SK_Params', $scheduleItem->getActionConfiguration());
        $this->assertSame(array('foo' => 'bar'), $scheduleItem->getActionConfiguration()->getParamsEncoded());

        $scheduleItem->setActionConfiguration();

        $this->assertSame(array(), $scheduleItem->getActionConfiguration()->getParamsEncoded());
    }

    public function testSetOffset() {
        $scheduleItem = SKTest_TH::createEntertainmentScheduleItem(null, null, 3);
        $this->assertSame(3, $scheduleItem->getOffset());
        $scheduleItem->setOffset(10000);
        $this->assertSame(10000, $scheduleItem->getOffset());
    }

    public function testSetSourceUserTemplate() {
        $scheduleItem = SKTest_TH::createEntertainmentScheduleItem(null, null, null, 4);
        $this->assertSame(4, $scheduleItem->getSourceUserTemplate());
        $scheduleItem->setSourceUserTemplate(2);
        $this->assertSame(2, $scheduleItem->getSourceUserTemplate());
        $scheduleItem->setSourceUserTemplate(null);
        $this->assertNull($scheduleItem->getSourceUserTemplate());
    }
}
