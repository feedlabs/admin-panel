<?php

class SK_ActionLimit_AbstractTest extends SKTest_TestCase {

    public static function setUpBeforeClass() {
        CM_Db_Db::insert('cm_actionLimit', array('actionType', 'actionVerb', 'type', 'role', 'limit', 'period'),
            array(
                array(SK_Action_Entity_Blogpost::getTypeStatic(), SK_Action_Abstract::PIN, SK_ActionLimit_Bruteforce::getTypeStatic(),
                    SK_Role::FREEUSER,
                    5, 3600),
                array(SK_Action_Entity_Blogpost::getTypeStatic(), SK_Action_Abstract::PIN, SK_ActionLimit_Bruteforce::getTypeStatic(), null, 17,
                    3600),
                array(SK_Action_Entity_Blogpost::getTypeStatic(), SK_Action_Abstract::PIN, SK_ActionLimit_Bruteforce::getTypeStatic(),
                    SK_Role::PREMIUMUSER, null, 3600),
            ));
    }

    public function testConstructor() {
        $actionLimit = new SK_ActionLimit_Bruteforce(SK_Action_Entity_Blogpost::getTypeStatic(), SK_Action_Abstract::PIN);
        $this->assertEquals(5, $actionLimit->getLimit(SK_Role::FREEUSER));
        $this->assertEquals(17, $actionLimit->getLimit(null));
        $this->assertNull($actionLimit->getLimit(SK_Role::PREMIUMUSER));
    }

    public function testSetData() {
        $actionLimit = new SK_ActionLimit_Bruteforce(SK_Action_Entity_Blogpost::getTypeStatic(), SK_Action_Abstract::PIN);
        $this->assertEquals(5, $actionLimit->getLimit(SK_Role::FREEUSER));
        $this->assertEquals(3600, $actionLimit->getPeriod(SK_Role::FREEUSER));
        $this->assertNull($actionLimit->getLimit(SK_Role::PREMIUMUSER));
        $actionLimit->setLimit(SK_Role::FREEUSER, 6);
        $actionLimit->setPeriod(SK_Role::FREEUSER, 3601);
        $this->assertEquals(6, $actionLimit->getLimit(SK_Role::FREEUSER));
        $this->assertEquals(3601, $actionLimit->getPeriod(SK_Role::FREEUSER));
        $actionLimit = new SK_ActionLimit_Bruteforce(SK_Action_Entity_Blogpost::getTypeStatic(), SK_Action_Abstract::PIN);
        $this->assertEquals(6, $actionLimit->getLimit(SK_Role::FREEUSER));
        $this->assertEquals(3601, $actionLimit->getPeriod(SK_Role::FREEUSER));

        $actionLimit = new SK_ActionLimit_Daily(SK_Action_User::getTypeStatic(), SK_Action_Abstract::CONNECT);
        $actionLimit->setLimit(null, null);
        $actionLimit->setPeriod(null, 50000);
        $actionLimit->setLimit(SK_Role::FREEUSER, 8);
        $actionLimit->setPeriod(SK_Role::FREEUSER, 86400);
        $this->assertEquals(8, $actionLimit->getLimit(SK_Role::FREEUSER));
        $this->assertEquals(86400, $actionLimit->getPeriod(SK_Role::FREEUSER));
        $this->assertNull($actionLimit->getLimit(SK_Role::PREMIUMUSER));
        $this->assertEquals(50000, $actionLimit->getPeriod(SK_Role::PREMIUMUSER));
    }

    public function testFactory() {
        $actionLimit = CM_Model_ActionLimit_Abstract::factory(SK_ActionLimit_Daily::getTypeStatic(), 1, 1);
        $this->assertInstanceOf('SK_ActionLimit_Daily', $actionLimit);
        $actionLimit = CM_Model_ActionLimit_Abstract::factory(SK_ActionLimit_Bruteforce::getTypeStatic(), 1, 1);
        $this->assertInstanceOf('SK_ActionLimit_Bruteforce', $actionLimit);
    }
}
