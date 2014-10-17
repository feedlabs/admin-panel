<?php

class SK_ModelAsset_User_ReviewsTest extends SKTest_TestCase {

    public function testAddReportedCorrectly() {
        $offender = SKTest_TH::createUser();
        $reporter = SKTest_TH::createUser();
        $blogpost = SKTest_TH::createBlogpost($offender);
        $offender->getProfile()->getReports()->add(SK_ModelAsset_Entity_Reports::SPAM, $reporter);
        $blogpost->getReports()->add(SK_ModelAsset_Entity_Reports::SPAM, $reporter);
        $blogpost->getReports()->add(SK_ModelAsset_Entity_Reports::OTHER, $reporter);
        $this->assertSame(0, $reporter->getReputationChanges()->getCount());
        $this->assertSame(0.0, $reporter->getReputation()->get());

        $offender->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM);

        $this->assertSame(1, $reporter->getReputationChanges()->getCount());
        SKTest_TH::reinstantiateModel($reporter);
        $this->assertGreaterThan(0.0, $reporter->getReputation()->get());

        $reviewer = SKTest_TH::createUser();
        $offender->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $reviewer);

        $this->assertSame(1, $reporter->getReputationChanges()->getCount());

        SKTest_TH::timeForward(SK_ReputationChange_ReportedCorrectly::TIMEFRAME + 1);

        $offender->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $reviewer);

        $this->assertSame(2, $reporter->getReputationChanges()->getCount());

        SKTest_TH::timeForward(SK_ReputationChange_ReportedCorrectly::TIMEFRAME + 1);

        $offender->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $reviewer);

        $this->assertSame(3, $reporter->getReputationChanges()->getCount());

        SKTest_TH::timeForward(SK_ReputationChange_ReportedCorrectly::TIMEFRAME + 1);

        $offender->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM);

        $this->assertSame(3, $reporter->getReputationChanges()->getCount());
    }

    public function  testAddReportedIncorrectly() {
        $offender = SKTest_TH::createUser();
        $reporter = SKTest_TH::createUser();
        $blogpost = SKTest_TH::createBlogpost($offender);
        $offender->getProfile()->getReports()->add(SK_ModelAsset_Entity_Reports::INAPPROPRIATE, $reporter);
        $blogpost->getReports()->add(SK_ModelAsset_Entity_Reports::SPAM, $reporter);

        $blogpost->getReports()->add(SK_ModelAsset_Entity_Reports::INAPPROPRIATE, $reporter);
        $this->assertSame(0, $reporter->getReputationChanges()->getCount());
        $this->assertSame(0.0, $reporter->getReputation()->get());

        $offender->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM);

        $this->assertSame(2, $reporter->getReputationChanges()->getCount());
        SKTest_TH::reinstantiateModel($reporter);
        $this->assertLessThan(0.0, $reporter->getReputation()->get());

        SKTest_TH::timeForward(1);

        $reviewer = SKTest_TH::createUser();
        $offender->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $reviewer);

        $this->assertSame(4, $reporter->getReputationChanges()->getCount());

        SKTest_TH::timeForward(1);

        $offender->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $reviewer);

        $this->assertSame(6, $reporter->getReputationChanges()->getCount());

        SKTest_TH::timeForward(1);

        $offender->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM);

        $this->assertSame(6, $reporter->getReputationChanges()->getCount());
    }

    public function testAddReputationChangeLegit() {
        $user = SKTest_TH::createUser();
        $admin = SKTest_TH::createUser();
        $this->assertTrue($user->getReputationChanges(SK_ReputationChange_ReviewLegit::getTypeStatic())->isEmpty());

        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_LEGIT, $admin);
        $this->assertSame(1, $user->getReputationChanges(SK_ReputationChange_ReviewLegit::getTypeStatic())->getCount());

        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_LEGIT, $admin);
        $this->assertSame(1, $user->getReputationChanges(SK_ReputationChange_ReviewLegit::getTypeStatic())->getCount());

        SKTest_TH::timeForward(SK_ReputationChange_ReviewLegit::TIMEFRAME + 1);

        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_LEGIT, $admin);
        $this->assertSame(2, $user->getReputationChanges(SK_ReputationChange_ReviewLegit::getTypeStatic())->getCount());
    }

    public function testAdd() {
        $user = SKTest_TH::createUser();
        $admin = SKTest_TH::createUser();

        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_MALE, $admin);
        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $admin);
        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_LEGIT, $admin);
        $this->assertEquals(3, $user->getReviews()->get()->getCount());
    }

    public function testAddSpamShouldRemoveReviewCandidate() {
        $user = SKTest_TH::createUser();
        $admin = SKTest_TH::createUser();

        $entity = SKTest_TH::createPhoto($user);
        SK_Model_ReviewCandidate::create($entity);
        $this->assertTrue(SK_Model_ReviewCandidate::exists($entity));

        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $admin);
        $this->assertFalse(SK_Model_ReviewCandidate::exists($entity));
    }

    public function testProcessPending() {
        CM_Config::get()->SK_ModelAsset_User_Reviews->processPendingEnabled = true;

        $user = SKTest_TH::createUser();
        $admin = SKTest_TH::createUser();

        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_MALE, $admin);
        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $admin);
        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_LEGIT, $admin);
        $this->assertEquals(3, $user->getReviews()->get()->getCount());

        SKTest_TH::timeDaysForward(5);
        SK_ModelAsset_User_Reviews::processPending();
        $this->assertEquals(3, $user->getReviews()->get()->getCount());
        try {
            new SK_User($user->getId());
            $this->assertTrue(true);
        } catch (CM_Exception_Nonexistent $ex) {
            $this->fail('User should still exist');
        }

        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM);
        $this->assertEquals(4, $user->getReviews()->get()->getCount());
        try {
            new SK_User($user->getId());
            $this->assertTrue(true);
        } catch (CM_Exception_Nonexistent $ex) {
            $this->fail('User should still exist');
        }

        SKTest_TH::timeDaysForward(2);
        SK_ModelAsset_User_Reviews::processPending();
        $this->assertEquals(4, $user->getReviews()->get()->getCount());
        try {
            new SK_User($user->getId());
            $this->assertTrue(true);
        } catch (CM_Exception_Nonexistent $ex) {
            $this->fail('User should still exist');
        }

        SKTest_TH::timeDaysForward(3);
        SK_ModelAsset_User_Reviews::processPending();
        $this->assertEquals(4, $user->getReviews()->get()->getCount());
        try {
            new SK_User($user->getId());
            $this->fail("User wasn't deleted after having been reviewed as SPAM");
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }

    public function testProcessPendingDisabled() {
        CM_Config::get()->SK_ModelAsset_User_Reviews->processPendingEnabled = false;

        $user = SKTest_TH::createUser();
        $admin = SKTest_TH::createUser();

        $user->getReviews()->add(SK_ModelAsset_User_Reviews::TYPE_SPAM, $admin);
        SKTest_TH::timeDaysForward(10);
        SK_ModelAsset_User_Reviews::processPending();
        try {
            new SK_User($user->getId());
            $this->assertTrue(true);
        } catch (CM_Exception_Nonexistent $ex) {
            $this->fail('User should not be deleted if processPendingEnabled=false');
        }
    }
}
