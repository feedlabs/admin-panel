<?php

class SK_Entity_CommentTest extends SKTest_TestCase {

    public function testConstruct() {
        $commentId = SKTest_TH::createComment('123')->getId();
        $comment = new SK_Entity_Comment($commentId);
        $this->assertInstanceOf('SK_Entity_Comment', $comment);
        try {
            new SK_Entity_Comment(12345);
            $this->fail('Can instantiate nonexistent comment');
        } catch (CM_Exception_Nonexistent $e) {
        }
    }

    public function testCreate() {
        $text = 'hello world';
        $entity = SKTest_TH::createBlogpost();
        $user = SKTest_TH::createUser();
        /** @var $comment SK_Entity_Comment */
        $time = time();
        $comment = SK_Entity_Comment::create($user, $entity, $text);
        $this->assertInstanceOf('SK_Entity_Comment', $comment);
        $this->assertRow('sk_entity_comment', array('text' => $text, 'entityType' => $entity->getType(), 'entity' => $entity->getId()));
        $this->assertSame($text, $comment->getText());
        $this->assertSameTime($time, $comment->getCreated());
        $this->assertSame($comment->getEntityType(), $entity->getType());
        $this->assertSame($comment->getEntityId(), $entity->getId());
        $this->assertEquals($entity, $comment->getEntity());
        $this->assertEquals($user, $comment->getUser());
    }

    public function testDelete() {
        $comment = SKTest_TH::createComment('123');
        $comment->delete();
        try {
            new SK_Entity_Comment($comment->getId());
            $this->fail('Can instantiate deleted comment');
        } catch (CM_Exception_Nonexistent $ex) {
            $this->assertTrue(true);
        }
    }

    public function testUpdateAggregation() {
        $entity = SKTest_TH::createBlogpost();
        $comment = SKTest_TH::createComment('foo', $entity);
        $comment2 = SKTest_TH::createComment('bar', $entity);
        $this->assertRow('sk_tmp_blogpost', array('blogpostId' => $entity->getId(), 'commentCount' => 2));
        $comment->delete();
        $this->assertRow('sk_tmp_blogpost', array('blogpostId' => $entity->getId(), 'commentCount' => 1));
        $comment2->delete();
        $this->assertRow('sk_tmp_blogpost', array('blogpostId' => $entity->getId(), 'commentCount' => 0));
        $user = SKTest_TH::createUser();
        SKTest_TH::createComment('spam', $entity, $user);
        $this->assertRow('sk_tmp_blogpost', array('blogpostId' => $entity->getId(), 'commentCount' => 1));
        $user->setBlocked();
        $this->assertRow('sk_tmp_blogpost', array('blogpostId' => $entity->getId(), 'commentCount' => 0));
        $user->setBlocked(false);
        $this->assertRow('sk_tmp_blogpost', array('blogpostId' => $entity->getId(), 'commentCount' => 1));
    }
}
