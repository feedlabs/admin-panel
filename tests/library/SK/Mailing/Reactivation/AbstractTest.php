<?php

class SK_Mailing_Reactivation_AbstractTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testMailing() {
        $user1 = SKTest_TH::createUser()->setEmailVerified();
        SKTest_TH::createUser()->setEmailVerified();
        CM_Db_Db::insert('sk_mailing', array('type'      => 1, 'userId' => $user1->getId(), 'checkStamp' => time(),
                                             'sendStamp' => $user1->getLatestactivity() - 1));
        SKTest_TH::timeForward(100);
        SKTest_TH::createUser()->setEmailVerified();
        SKTest_TH::timeForward(-50);

        $mailing = $this->getMockForAbstractClass('SK_Mailing_Reactivation_Abstract', array(null), '', true, true, true, array('getType', '_getLimit',
            '_getInterval', '_getMail'));
        $mailing->expects($this->any())->method('getType')->will($this->returnValue(1));
        $mailing->expects($this->any())->method('_getInterval')->will($this->returnValue(0));
        $mailing->expects($this->any())->method('_getLimit')->will($this->returnValue(null));
        $mailing->expects($this->exactly(2))->method('_getMail')->will($this->returnValue($this->getMockForAbstractClass('CM_Mail', array(), '', true, true, true, array('sendDelayed'))));

        $mailing->send(10);
        SKTest_TH::timeForward(1000);
        $mailing->send(10);
    }
}
