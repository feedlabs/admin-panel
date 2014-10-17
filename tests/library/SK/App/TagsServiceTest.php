<?php

class SK_App_TagsServiceTest extends SKTest_TestCase {

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testCreateAggregation() {
        $photo1 = SKTest_TH::createPhoto();
        $photo1->getTags()->add('foo');
        $photo2 = SKTest_TH::createPhoto();
        $photo2->getTags()->add('foo');
        $photo2->getTags()->add('bar');

        $video1 = SKTest_TH::createVideo();
        $video1->getTags()->add('foo');
        $video2 = SKTest_TH::createVideo();
        $video2->getTags()->add('foo');
        $video2->getTags()->add('bar');

        $blogpost1 = SKTest_TH::createBlogpost();
        $blogpost1->getTags()->add('foo');
        $blogpost2 = SKTest_TH::createBlogpost();
        $blogpost2->getTags()->add('foo');
        $blogpost2->getTags()->add('bar');
        $blogpost2->getTags()->add('foobar');

        SK_App_TagsService::createAggregation();

        $this->assertSame('6', CM_Db_Db::select('sk_tag', 'count', array('label' => 'foo'))->fetchColumn());
        $this->assertSame('3', CM_Db_Db::select('sk_tag', 'count', array('label' => 'bar'))->fetchColumn());
        $this->assertSame('1', CM_Db_Db::select('sk_tag', 'count', array('label' => 'foobar'))->fetchColumn());

        $blogpost2->delete();

        SK_App_TagsService::createAggregation();

        $this->assertSame('5', CM_Db_Db::select('sk_tag', 'count', array('label' => 'foo'))->fetchColumn());
        $this->assertSame('2', CM_Db_Db::select('sk_tag', 'count', array('label' => 'bar'))->fetchColumn());
        $this->assertNotRow('sk_tag', array('label' => 'foobar'));
    }
}
