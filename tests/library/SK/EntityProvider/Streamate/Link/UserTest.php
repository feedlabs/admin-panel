<?php

class SK_EntityProvider_Streamate_Link_UserTest extends SKTest_TestCase {

    public function testFindModel() {
        $providerId = 666;
        $user = SKTest_TH::createUser();
        CM_Db_Db::insert('sk_entityProvider_streamate_user', array('modelId' => $user->getId(), 'providerId' => $providerId));
        $linkUser = new SK_EntityProvider_Streamate_Link_User();
        $this->assertEquals($user, $linkUser->findModel($providerId));

        $user->delete();
        $this->assertNull($linkUser->findModel($providerId));
    }

    public function testLinkModel() {
        $providerId = 20;
        $user = SKTest_TH::createUser();
        $linkUser = new SK_EntityProvider_Streamate_Link_User();
        $this->assertNull($linkUser->findModel($providerId));
        $linkUser->linkModel($providerId, $user);
        $this->assertEquals($user, $linkUser->findModel($providerId));
    }

    public function testUnlinkModel() {
        $providerId = 90;
        $user = SKTest_TH::createUser();
        CM_Db_Db::insert('sk_entityProvider_streamate_user', array('modelId' => $user->getId(), 'providerId' => $providerId));
        $linkUser = new SK_EntityProvider_Streamate_Link_User();
        $linkUser->unlinkModel($user);
        $this->assertNull($linkUser->findModel($providerId));
    }
}
