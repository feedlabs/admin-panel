<?php

class SK_ReputationChange_ReviewLegitTest extends SKTest_TestCase {

    public function testCreate() {
        $user = SKTest_TH::createUser();
        /** @var SK_ReputationChange_ReviewLegit $reputationChange */
        $reputationChange = SK_ReputationChange_ReviewLegit::createStatic(array('user' => $user));

        $this->assertInstanceOf('SK_ReputationChange_ReviewLegit', $reputationChange);
        $this->assertSame(30.0, $reputationChange->getValue());
        $this->assertSame(SK_ReputationChange_ReviewLegit::getTypeStatic(), $reputationChange->getType());
        $this->assertEquals($user, $reputationChange->getUser());
        $this->assertSame(null, $reputationChange->getInitiatingUser());
        $this->assertEquals(time(), $reputationChange->getCreated(), null, 1);
    }

    public function testCreateWithInitiatingUser() {
        $user = SKTest_TH::createUser();
        $initiatingUser = SKTest_TH::createUser();
        SK_ReputationChange_ReviewLegit::createStatic(array('user' => $initiatingUser));
        $initiatingUserWeight = $initiatingUser->getReputation()->getNormalizedWeight();
        $this->assertGreaterThan(0, $initiatingUserWeight);

        /** @var SK_ReputationChange_ReviewLegit $reputationChange */
        $reputationChange = SK_ReputationChange_ReviewLegit::createStatic(array('user' => $user, 'initiatingUser' => $initiatingUser));

        $this->assertInstanceOf('SK_ReputationChange_ReviewLegit', $reputationChange);
        $this->assertEquals(30.0 * $initiatingUserWeight, $reputationChange->getValue(), null, 0.0001);
        $this->assertSame(SK_ReputationChange_ReviewLegit::getTypeStatic(), $reputationChange->getType());
        $this->assertEquals($user, $reputationChange->getUser());
        $this->assertEquals($initiatingUser, $reputationChange->getInitiatingUser());
        $this->assertEquals(time(), $reputationChange->getCreated(), null, 1);
    }

    public function testDeleteInitiatingUser() {
        $user = SKTest_TH::createUser();
        $initiatingUser = SKTest_TH::createUser();
        /** @var SK_ReputationChange_ReviewLegit $reputationChange */
        $reputationChange = SK_ReputationChange_ReviewLegit::createStatic(array('user' => $user, 'initiatingUser' => $initiatingUser));

        $this->assertEquals($initiatingUser, $reputationChange->getInitiatingUser());

        $initiatingUser->delete();
        $this->assertNull($reputationChange->getInitiatingUser());
    }
}
