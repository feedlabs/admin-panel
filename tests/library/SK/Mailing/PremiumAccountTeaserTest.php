<?php

class SK_Mailing_PremiumAccountTeaserTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testMailing() {
        SKTest_TH::createUser()->setEmailVerified();
        SKTest_TH::createUser()->setEmailVerified();
        SKTest_TH::createUserPremium()->setEmailVerified();
        SKTest_TH::createUser(SK_User::SEX_FEMALE)->setEmailVerified();
        SKTest_TH::timeDaysForward(8);
        SKTest_TH::createUser()->setEmailVerified();

        $mailing = new SK_Mailing_PremiumAccountTeaser();
        $mailing->send(10);
        $this->assertSame(3, CM_Db_Db::count('sk_mailing', 'sendStamp IS NOT NULL'));

        SKTest_TH::timeDaysForward(8);
        $mailing->send(10);
        $this->assertSame(4, CM_Db_Db::count('sk_mailing', 'sendStamp IS NOT NULL'));
    }
}
