<?php

class SK_EntityProvider_AdultCentro_Link_UserTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testFindModel() {
        $user = SKTest_TH::createUser();
        CM_Db_Db::insert('sk_entityProvider_adultCentro_user', array('modelId' => $user->getId(), 'providerId' => 666));
        $linkUser = new SK_EntityProvider_AdultCentro_Link_User();
        $this->assertEquals($user, $linkUser->findModel(666));

        $user->delete();
        $this->assertNull($linkUser->findModel(666));
        $this->assertNull($linkUser->findModel(999));
    }

    public function testLinkModel() {
        $user = SKTest_TH::createUser();
        $linkUser = new SK_EntityProvider_AdultCentro_Link_User();
        $this->assertNull($linkUser->findModel(20));
        $linkUser->linkModel(20, $user);
        $this->assertEquals($user, $linkUser->findModel(20));
    }

    public function testUnlinkModel() {
        $user = SKTest_TH::createUser();
        CM_Db_Db::insert('sk_entityProvider_adultCentro_user', array('modelId' => $user->getId(), 'providerId' => 90));
        $linkUser = new SK_EntityProvider_AdultCentro_Link_User();
        $linkUser->unlinkModel($user);
        $this->assertNull($linkUser->findModel(90));
    }
}
