<?php

class SK_Entity_BlogpostTest extends SKTest_TestCase {

    public function testConstruct() {
        $blogpostId = SKTest_TH::createBlogpost()->getId();
        $blogpost = new SK_Entity_Blogpost($blogpostId);
        $this->assertInstanceOf('SK_Entity_Blogpost', $blogpost);

        try {
            new SK_Entity_Blogpost(12345);
            $this->fail('Can instantiate nonexistent blogpost');
        } catch (CM_Exception_Nonexistent $e) {
        }
    }

    public function testCreate() {
        $user = SKTest_TH::createUser();
        $this->assertEquals(0, $user->getBlogposts()->getCount());
        /** @var SK_Entity_Blogpost $blogpost */
        $blogpost = SK_Entity_Blogpost::create('TestPost', 'TestText', $user, SK_ModelAsset_Entity_PrivacyAbstract::NONE);
        $this->assertInstanceOf('SK_Entity_Blogpost', $blogpost);
        $this->assertSame('TestText', $blogpost->getText());
        $this->assertSame('TestPost', $blogpost->getTitle());
        $this->assertGreaterThan(0, $blogpost->getId());
        $this->assertEquals(1, $user->getBlogposts()->getCount());
        $this->assertEquals($user, $blogpost->getUser());
        $this->assertSameTime(time(), $blogpost->getCreated());
        $this->assertNull($blogpost->getPopularStamp());
        $this->assertSame(SK_ModelAsset_Entity_PrivacyAbstract::NONE, $blogpost->getPrivacy()->get());
    }

    public function testSetTitle() {
        $blogpost = SKTest_TH::createBlogpost();
        $blogpost->setTitle('hello');
        $this->assertEquals('hello', $blogpost->getTitle());
    }

    public function testSetText() {
        $blogpost = SKTest_TH::createBlogpost();
        $blogpost->setText('goodbye');
        $this->assertEquals('goodbye', $blogpost->getText());
    }

    public function testDelete() {
        $user = SKTest_TH::createUser();
        $blogpost = SKTest_TH::createBlogpost();

        $blogpost->getRating()->setScore($user, 1);
        $this->assertRow('sk_tmp_blogpost', array('blogpostId' => $blogpost->getId()));

        $blogpost->delete();
        $this->assertNotRow('sk_tmp_blogpost', array('blogpostId' => $blogpost->getId()));
    }

    public function testGetUser() {
        $user = SKTest_TH::createUser();
        $blogpost = SKTest_TH::createBlogpost($user);
        $this->assertEquals($user, $blogpost->getUser());
    }

    public function testGetSetPopular() {
        $blogpost = SKTest_TH::createBlogpost();
        $this->assertNull($blogpost->getPopularStamp());
        $blogpost->setPopular(true);
        $this->assertSame(time(), $blogpost->getPopularStamp());
        $blogpost->setPopular(false);
        $this->assertNull($blogpost->getPopularStamp());
    }
}
