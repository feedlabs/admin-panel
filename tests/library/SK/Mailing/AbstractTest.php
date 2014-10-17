<?php

class SK_Mailing_AbstractTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testMailing() {
        $user1 = SKTest_TH::createUser()->setEmailVerified();
        $user2 = SKTest_TH::createUser()->setEmailVerified();
        SKTest_TH::createUser()->setEmailVerified();
        CM_Db_Db::insert('sk_mailing', array('type' => 1, 'userId' => $user1->getId(), 'checkStamp' => time() + 50, 'sendStamp' => time()));
        CM_Db_Db::insert('sk_mailing', array('type' => 1, 'userId' => $user2->getId(), 'checkStamp' => time(), 'sendStamp' => time()));
        $mailing = $this->getMockForAbstractClass('SK_Mailing_Abstract', array(null), '', false, true, true, array('getType', '_getLimit',
            '_getInterval', '_getMail'));
        $mailing->expects($this->any())->method('getType')->will($this->returnValue(1));
        $mailing->expects($this->any())->method('_getInterval')->will($this->returnValue(99));
        $mailing->expects($this->any())->method('_getLimit')->will($this->returnValue(null));
        $mailing->expects($this->exactly(2))->method('_getMail')->will($this->returnValue($this->getMockForAbstractClass('CM_Mail', array(), '', true, true, true, array('sendDelayed'))));
        SKTest_TH::timeForward(100);
        $mailing->send(10);

        SKTest_TH::timeForward(55);
        $mailing = $this->getMockForAbstractClass('SK_Mailing_Abstract', array(null), '', false, true, true, array('getType', '_getLimit',
            '_getInterval', '_getMail'));
        $mailing->expects($this->any())->method('getType')->will($this->returnValue(1));
        $mailing->expects($this->any())->method('_getInterval')->will($this->returnValue(99));
        $mailing->expects($this->any())->method('_getLimit')->will($this->returnValue(null));
        $mailing->expects($this->exactly(1))->method('_getMail')->will($this->returnValue($this->getMockForAbstractClass('CM_Mail', array(), '', true, true, true, array('sendDelayed'))));

        $mailing->send(10);
    }

    public function testMailingWithLimit() {
        $user1 = SKTest_TH::createUser()->setEmailVerified();
        $user2 = SKTest_TH::createUser()->setEmailVerified();
        $user3 = SKTest_TH::createUser()->setEmailVerified();
        CM_Db_Db::insert('sk_mailing', array('type' => 1, 'userId' => $user1->getId(), 'checkStamp' => time(), 'sendStamp' => time(), 'count' => 1));
        CM_Db_Db::insert('sk_mailing', array('type' => 1, 'userId' => $user2->getId(), 'checkStamp' => time(), 'sendStamp' => time()));
        $mailing = $this->getMockForAbstractClass('SK_Mailing_Abstract', array(null), '', false, true, true, array('getType', '_getLimit',
            '_getInterval', '_getMail'));
        $mailing->expects($this->any())->method('getType')->will($this->returnValue(1));
        $mailing->expects($this->any())->method('_getInterval')->will($this->returnValue(99));
        $mailing->expects($this->any())->method('_getLimit')->will($this->returnValue(1));
        $mailing->expects($this->exactly(2))->method('_getMail')->will($this->returnValue(null));
        SKTest_TH::timeForward(100);
        $mailing->send(10);
    }
}
