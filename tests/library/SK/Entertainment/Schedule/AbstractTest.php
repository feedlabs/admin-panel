<?php

class SK_Entertainment_Schedule_AbstractTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testLoadDataWrongType() {
        $schedule = SK_Entertainment_Schedule_Offline::createStatic(array('description' => null));
        try {
            new SK_Entertainment_Schedule_Online($schedule->getId());
            $this->fail("Instantiated object of `SK_Entertainment_Schedule_Offline` as `SK_Entertainment_Schedule_Online`");
        } catch (CM_Exception_Invalid $ex) {
            $this->assertContains('Invalid type `' . SK_Entertainment_Schedule_Offline::getTypeStatic() .
                '` for `SK_Entertainment_Schedule_Online` (type: `' .
                SK_Entertainment_Schedule_Online::getTypeStatic() . '`)', $ex->getMessage());
        }
    }

    public function testCreate() {
        /** @var SK_Entertainment_Schedule_Offline $schedule */
        $schedule = SK_Entertainment_Schedule_Offline::createStatic(array('description' => 'foo'));
        $this->assertInstanceOf('SK_Entertainment_Schedule_Abstract', $schedule);
        $this->assertSame('foo', $schedule->getDescription());
        $this->assertFalse($schedule->getEnabled());
    }

    public function testDelete() {
        $schedule = SK_Entertainment_Schedule_Offline::createStatic(array('description' => null));
        $scheduleItem = SKTest_TH::createEntertainmentScheduleItem($schedule);
        $schedule->delete();
        try {
            SKTest_TH::reinstantiateModel($schedule);
            $this->fail("Could reinstantiate deleted schedule.");
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
        try {
            SKTest_TH::reinstantiateModel($scheduleItem);
            $this->fail("Could reinstantiate deleted scheduleItem.");
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }

    public function testExecute() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;

        $type = new SK_Elasticsearch_Type_User();
        $searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $searchIndexCli->create($type->getIndex()->getName());

        $config = CM_Config::get();
        $config->SK_Entertainment_Schedule_Abstract->executingEnabled = true;

        for ($i = 0; $i < 5; $i++) {
            SKTest_TH::createUser(SK_User::SEX_FEMALE)->getRoles()->add(SK_Role::ENTERTAINER);
        }

        CM_Model_Location::createAggregation();
        $searchIndexCli->update($type->getIndex()->getName());
        $type->getIndex()->refresh();
        $targetUser = SKTest_TH::createUser();

        $schedule = SK_Entertainment_Schedule_Online::createStatic(array('description' => null));
        $scheduleItem = SKTest_TH::createEntertainmentScheduleItem($schedule, SK_Entertainment_Action_FriendRequest::getTypeStatic(), 100, 3);
        $schedule->execute($targetUser);

        /** @var SK_Entertainment_Schedule_Abstract $schedule */
        $schedule = SK_Entertainment_Schedule_Offline::createStatic(array('description' => null));
        $scheduleItem1 = SKTest_TH::createEntertainmentScheduleItem($schedule, SK_Entertainment_Action_FriendRequest::getTypeStatic(), 100, 3);
        $scheduleItem2 = SKTest_TH::createEntertainmentScheduleItem($schedule, SK_Entertainment_Action_FriendRequest::getTypeStatic(), 200, 3);
        $time = time();

        $this->assertSame(1, CM_Db_Db::count('sk_userAction'));
        $schedule->execute($targetUser);
        $this->assertSame(2, CM_Db_Db::count('sk_userAction'));

        $query = "SELECT * FROM `sk_userAction` ORDER BY `executeStamp` ASC LIMIT 1";

        $_getAction = SKTest_TH::getProtectedMethod('SK_User_ActionQueue', '_getAction');
        $actionRow1 = CM_Db_Db::exec($query)->fetch();
        $actionRow2 = CM_Db_Db::exec($query . ',1')->fetch();

        /** @var SK_User_Action_Abstract $action1 */
        $action1 = $_getAction->invoke(null, $actionRow1);
        $this->assertSame($scheduleItem1->getActionType(), $action1->getType());
        $this->assertEquals($targetUser, $action1->getUser());
        $this->assertSame($time + 100, $action1->getExecuteStamp());

        /** @var SK_User_Action_Abstract $action2 */
        $action2 = $_getAction->invoke(null, $actionRow2);
        $this->assertSame($scheduleItem1->getActionType(), $action2->getType());
        $this->assertEquals($targetUser, $action2->getUser());
        $this->assertSame($time + 200, $action2->getExecuteStamp());

        $this->assertEquals($action1->getParams()->getUser('sourceUser'), $action2->getParams()->getUser('sourceUser'));
    }

    public function testExecuteDisabled() {
        $config = CM_Config::get();
        $config->SK_Entertainment_Schedule_Abstract->executingEnabled = false;
        /** @var SK_Entertainment_Schedule_Abstract $schedule */
        $schedule = SK_Entertainment_Schedule_Offline::createStatic(array('description' => null));
        SKTest_TH::createEntertainmentScheduleItem($schedule, 1, 100, 3);
        $targetUser = SKTest_TH::createUser();

        $schedule->execute($targetUser);

        $this->assertNotRow('sk_userAction', null);
    }

    public function testSetDescription() {
        /** @var SK_Entertainment_Schedule_Offline $schedule */
        $schedule = SK_Entertainment_Schedule_Offline::createStatic(array('description' => null));
        $this->assertSame('', $schedule->getDescription());
        $schedule->setDescription('foo');
        $this->assertSame('foo', $schedule->getDescription());
        $schedule->setDescription(null);
        $this->assertSame('', $schedule->getDescription());
    }

    public function testSetEnabled() {
        /** @var SK_Entertainment_Schedule_Offline $schedule */
        $schedule = SK_Entertainment_Schedule_Offline::createStatic(array('description' => null));
        $this->assertFalse($schedule->getEnabled());
        $schedule->setEnabled();
        $this->assertTrue($schedule->getEnabled());
        $schedule->setEnabled(false);
        $this->assertFalse($schedule->getEnabled());
    }

    public function testFactory() {
        $scheduleOnline = SK_Entertainment_Schedule_Offline::createStatic(array('description' => null));
        $scheduleOffline = SK_Entertainment_Schedule_Online::createStatic(array('description' => null));
        $this->assertEquals($scheduleOffline, SK_Entertainment_Schedule_Abstract::factory($scheduleOffline->getId()));
        $this->assertEquals($scheduleOnline, SK_Entertainment_Schedule_Abstract::factory($scheduleOnline->getId()));
    }

    public function testFactoryNonexistentId() {
        try {
            SK_Entertainment_Schedule_Abstract::factory(3);
            $this->fail('Fetched nonexistent schedule');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertContains('No record found in `', $ex->getMessage());
        }
        try {
            SK_Entertainment_Schedule_Abstract::factory(3, 2);
            $this->fail('Fetched nonexistent schedule');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertContains('has no data', $ex->getMessage());
        }
    }
}
