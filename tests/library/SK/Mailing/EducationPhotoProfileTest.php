<?php

class SK_Mailing_EducationPhotoProfileTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testMailing() {
        $i = 0;
        while ($i < 3) {
            SKTest_TH::createUser()->setEmailVerified();
            $i++;
        }
        SKTest_TH::timeForward(86420);
        SKTest_TH::createUser()->setEmailVerified();

        $mailing = new SK_Mailing_EducationPhotoProfile();
        $mailing->send(10);
        $this->assertSame(3, CM_Db_Db::count('sk_mailing', 'sendStamp IS NOT NULL'));

        SKTest_TH::timeForward(86420);
        $mailing->send(10);
        $this->assertSame(4, CM_Db_Db::count('sk_mailing', 'sendStamp IS NOT NULL'));
    }
}
