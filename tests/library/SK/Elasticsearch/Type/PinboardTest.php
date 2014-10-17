<?php

class SK_Elasticsearch_Type_PinboardTest extends SKTest_TestCase {

    /** @var SK_Elasticsearch_Type_Pinboard */
    protected $_type;

    /** @var CM_Elasticsearch_Index_Cli */
    protected $_searchIndexCli;

    public function setUp() {
        CM_Config::get()->CM_Elasticsearch_Client->enabled = true;

        $this->_type = new SK_Elasticsearch_Type_Pinboard();
        $this->_searchIndexCli = new CM_Elasticsearch_Index_Cli();
        $this->_searchIndexCli->create($this->_type->getIndex()->getName());
    }

    public function tearDown() {
        $this->_type->getIndex()->delete();
        SKTest_TH::clearEnv();
    }

    public function testEntertainer() {
        $user = SKTest_TH::createUser();
        SKTest_TH::createPinboard($user);
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Pinboard();
        $searchQuery->filterRoleNot(SK_Role::ENTERTAINER);

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $paging->getCount());

        $user->getRoles()->add(SK_Role::ENTERTAINER);
        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $paging = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(0, $paging->getCount());
    }

    public function testRatingCount() {
        $user1 = SKTest_TH::createUser();
        $pinBoard1 = SKTest_TH::createPinboard($user1);
        $pinBoard1->getRating()->setScore(SKTest_TH::createUser(), 1);

        $user2 = SKTest_TH::createUser();
        $pinBoard2 = SKTest_TH::createPinboard($user2);
        $pinBoard2->getRating()->setScore(SKTest_TH::createUser(), -1);
        $pinBoard2->getRating()->setScore(SKTest_TH::createUser(), -1);

        $user3 = SKTest_TH::createUser();
        $pinBoard3 = SKTest_TH::createPinboard($user3);
        $pinBoard3->getRating()->setScore(SKTest_TH::createUser(), 1);
        $pinBoard3->getRating()->setScore(SKTest_TH::createUser(), 1);
        $pinBoard3->getRating()->setScore(SKTest_TH::createUser(), -1);

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        foreach (array(1 => $pinBoard1, 2 => $pinBoard2, 3 => $pinBoard3) as $ratingCount => $pinBoard) {
            $searchQuery = new SK_Elasticsearch_Query_Entity_Pinboard();
            $searchQuery->filterRatingCount($ratingCount, $ratingCount);

            $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
            $this->assertSame(1, $pagingSource->getCount());
            $this->assertContainsAll(array($pinBoard->getId()), $pagingSource->getItems());
        }
    }

    public function testViewCount() {
        $pinBoard1 = SKTest_TH::createPinboard();
        $pinBoard1->getViews()->track();

        $pinBoard2 = SKTest_TH::createPinboard();
        $pinBoard2->getViews()->track();
        $pinBoard2->getViews()->track();

        $pinBoard3 = SKTest_TH::createPinboard();
        $pinBoard3->getViews()->track();
        $pinBoard3->getViews()->track();
        $pinBoard3->getViews()->track();

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        foreach (array(1 => $pinBoard1, 2 => $pinBoard2, 3 => $pinBoard3) as $viewCount => $pinBoard) {
            $searchQuery = new SK_Elasticsearch_Query_Entity_Pinboard();
            $searchQuery->filterViewCount($viewCount, $viewCount);

            $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
            $this->assertSame(1, $pagingSource->getCount());
            $this->assertContainsAll(array($pinBoard->getId()), $pagingSource->getItems());
        }
    }

    public function testPinCount() {
        $pinBoard1 = SKTest_TH::createPinboard();
        $pinBoard1->add(SKTest_TH::createUser()->getProfile());

        $pinBoard2 = SKTest_TH::createPinboard();
        $pinBoard2->add(SKTest_TH::createPhoto());
        $pinBoard2->add(SKTest_TH::createBlogpost());

        $pinBoard3 = SKTest_TH::createPinboard();
        $pinBoard3->add(SKTest_TH::createBlogpost());
        $pinBoard3->add(SKTest_TH::createVideo());
        $pinBoard3->add(SKTest_TH::createPinboard());

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        foreach (array(1 => $pinBoard1, 2 => $pinBoard2, 3 => $pinBoard3) as $pinCount => $pinBoard) {
            $searchQuery = new SK_Elasticsearch_Query_Entity_Pinboard();
            $searchQuery->filterPinCount($pinCount, $pinCount);

            $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
            $this->assertSame(1, $pagingSource->getCount());
            $this->assertContainsAll(array($pinBoard->getId()), $pagingSource->getItems());
        }
    }

    public function testSetBirthdate() {
        $user1 = SKTest_TH::createUser();
        $user1->setBirthdate(new \DateTime('-20 years'));
        $pinboard1 = SKTest_TH::createPinboard($user1);

        $user2 = SKTest_TH::createUser();
        $user2->setBirthdate(new \DateTime('-23 years'));
        $pinboard2 = SKTest_TH::createPinboard($user2);

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $searchQuery = new SK_Elasticsearch_Query_Entity_Pinboard();
        $searchQuery->filterAgeRange(18, 25);

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(2, $pagingSource->getCount());
        $this->assertContainsAll(array($pinboard1->getId(), $pinboard2->getId()), $pagingSource->getItems());

        $user1->setBirthdate(new \DateTime('-30 years'));

        $this->_searchIndexCli->update($this->_type->getIndex()->getName());

        $pagingSource = new CM_PagingSource_Elasticsearch($this->_type, $searchQuery);
        $this->assertSame(1, $pagingSource->getCount());
        $this->assertContainsAll(array($pinboard2->getId()), $pagingSource->getItems());
    }
}
