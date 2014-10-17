<?php

class SK_Service_PremiumTest extends SKTest_TestCase {

    public function testConstruct() {
        $premium = new SK_Service_Premium(12);
        $this->assertSame(12, $premium->getAmount());
    }

    public function testProcess() {
        $premium = new SK_Service_Premium(12);
        $user = SKTest_TH::createUser();
        $transaction = SKTest_TH::createPaymentTransaction($user->getId());
        $this->assertFalse($user->getRoles()->contains(SK_Role::PREMIUMUSER));
        $this->assertFalse($user->getRoles()->contains(SK_Role::VERIFIED_CONTACT));

        $premium->process($transaction);
        SKTest_TH::reinstantiateModel($user);

        $this->assertTrue($user->getRoles()->contains(SK_Role::PREMIUMUSER));
        $this->assertEquals(time() + 12 * 86400, $user->getRoles()->getExpirationStamp(SK_Role::PREMIUMUSER), null, 1);

        $this->assertTrue($user->getRoles()->contains(SK_Role::VERIFIED_CONTACT));
        $this->assertNull($user->getRoles()->getExpirationStamp(SK_Role::VERIFIED_CONTACT));
    }
}
