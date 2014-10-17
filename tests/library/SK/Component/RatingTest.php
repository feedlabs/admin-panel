<?php

class SK_Component_RatingTest extends SKTest_TestCase {

    public function testGuest() {
        $photo = SKTest_TH::createPhoto();
        $photo->getRating()->setScore(SKTest_TH::createUser(), 1);
        $cmp = new SK_Component_Rating(array('entity' => $photo));
        $page = $this->_renderComponent($cmp);

        $this->assertComponentAccessible($cmp);
        $this->assertTrue($page->has('.rating-bar'));
        $this->assertFalse($page->has('.setScoreUp'));
        $this->assertFalse($page->has('.setScoreDown'));
    }

    public function testFreeuser() {
        $viewer = $this->_createViewer();
        $photo = SKTest_TH::createPhoto();
        $photo->getRating()->setScore(SKTest_TH::createUser(), 1);
        $cmp = new SK_Component_Rating(array('entity' => $photo));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.rating-bar'));
        $this->assertTrue($page->has('.setScoreUp'));
        $this->assertTrue($page->has('.setScoreDown'));
    }

    public function testFreeuserOwner() {
        $viewer = $this->_createViewer();
        $cmp = new SK_Component_Rating(array('entity' => SKTest_TH::createVideo($viewer)));
        $page = $this->_renderComponent($cmp, $viewer);

        $this->assertComponentAccessible($cmp, $viewer);
        $this->assertTrue($page->has('.setScoreUp'));
        $this->assertTrue($page->has('.setScoreDown'));
    }

    public function testAjax_updateScore() {
        $viewer = SKTest_TH::createUser();
        $user = SKTest_TH::createUser();
        $entity = SKTest_TH::createBlogpost($user);
        $component = new SK_Component_Rating(['entity' => $entity]);

        $this->assertSame(0.0, $user->getReputation()->get());
        $this->assertTrue($user->getReputationChanges()->isEmpty());

        $environment1 = new CM_Frontend_Environment(null, $viewer);
        $this->getResponseAjax($component, 'updateScore', ['score' => 1, 'entity' => $entity], $environment1);

        SKTest_TH::reinstantiateModel($user);
        $this->assertGreaterThan(0.0, $user->getReputation()->get());
        $this->assertSame(1, $user->getReputationChanges()->getCount());
        /** @var SK_ReputationChange_Abstract $reputationChange */
        $reputationChange = $user->getReputationChanges()->getItem(0);
        $this->assertSame(SK_ReputationChange_Like::getTypeStatic(), $reputationChange->getType());
        $this->assertEquals($viewer, $reputationChange->getInitiatingUser());

        $environment2 = new CM_Frontend_Environment(null, SKTest_TH::createUser());
        $this->getResponseAjax($component, 'updateScore', ['score' => 1, 'entity' => $entity], $environment2);

        $this->assertSame(1, $user->getReputationChanges()->getCount());

        SKTest_TH::timeForward(86400);

        $environment3 = new CM_Frontend_Environment(null, SKTest_TH::createUser());
        $this->getResponseAjax($component, 'updateScore', ['score' => 1, 'entity' => $entity], $environment3);

        $this->assertSame(2, $user->getReputationChanges()->getCount());
    }
}
