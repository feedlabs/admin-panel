<?php

class SK_ModelAsset_Entity_TagsTest extends SKTest_TestCase {

    public function testTags() {
        $video = SKTest_TH::createVideo();

        $video->getTags()->set(array('peter', 'hans', 'jo'));
        $this->assertEquals(array('hans', 'jo', 'peter'), $video->getTags()->get());

        $videoId = $video->getId();
        $video->delete();
        $this->assertNotRow('sk_tag_entity', array('entityId' => $videoId, 'entityType' => SK_Entity_Video::getTypeStatic()));
    }

    public function testMultiTags() {
        $video1 = SKTest_TH::createVideo();
        $video2 = SKTest_TH::createVideo();

        $video1->getTags()->set(array('peter', 'hans', 'jo'));
        $video2->getTags()->set(array('zoom', 'hans', 'jo'));

        $this->assertRow('sk_tag', array('label' => 'hans'));

        $tagId = CM_Db_Db::select('sk_tag', 'id', array('label' => 'hans'))->fetchColumn();

        $this->assertEquals(array('hans', 'jo', 'peter'), $video1->getTags()->get());
        $this->assertEquals(array('hans', 'jo', 'zoom'), $video2->getTags()->get());
        $this->assertRow('sk_tag_entity', array('tagId' => $tagId, 'entityType' => SK_Entity_Video::getTypeStatic()), 2);
    }

    public function testSetStrangeKeys() {
        $video = SKTest_TH::createVideo();
        $tags = $video->getTags();

        $tags->set(array(0 => 'foo', 2 => 'bar'));
        $this->assertSame(array('bar', 'foo'), $tags->get());
    }

    public function testSetDuplicates() {
        $video = SKTest_TH::createVideo();
        $tags = $video->getTags();

        $tags->set(array('foo', 'foo'));
        $this->assertSame(array('foo'), $tags->get());
    }

    public function testEmpty() {
        $video = SKTest_TH::createVideo();
        $video->getTags()->add(array(''));
        $this->assertEmpty($video->getTags()->get());
        $video->getTags()->add(array(" ", "\n", "\0", "\r", "\t"));
        $this->assertEmpty($video->getTags()->get());
        $video->getTags()->add(array(" ", "\n\t\0\r", "\0\r\t\t\t\t", "\r \t \r \x0B", "\t"));
        $this->assertEmpty($video->getTags()->get());
    }

    public function testAdd() {
        $video = SKTest_TH::createVideo();
        $video->getTags()->add(array('bar', 'foo'));
        $this->assertSame(array('bar', 'foo'), $video->getTags()->get());

        $video->getTags()->add('foobar');
        $this->assertSame(array('bar', 'foo', 'foobar'), $video->getTags()->get());

        $video->getTags()->add('');
        $this->assertSame(array('bar', 'foo', 'foobar'), $video->getTags()->get());
    }

    public function testDelete() {
        $video = SKTest_TH::createVideo();
        $video->getTags()->set(array('foo', 'bar', 'foobar'));
        $this->assertSame(array('bar', 'foo', 'foobar'), $video->getTags()->get());

        $video->getTags()->delete('foobar');
        $this->assertSame(array('bar', 'foo'), $video->getTags()->get());
    }

    public function testNormalize() {
        $video = SKTest_TH::createVideo();
        $video->getTags()->add(array('@@   Foo  bar  @@', 'FOO   ==   BAR', 'Foo  +-!@#$%^&*()  Bar'));
        $this->assertSame(array('foo bar'), $video->getTags()->get());
        $video->getTags()->add('hans  ->   mit  %%  foo');
        $this->assertSame(array('foo bar', 'hans mit foo'), $video->getTags()->get());
    }

    public function testInternationalization() {
        $video = SKTest_TH::createVideo();
        foreach (
            array(
                'حيث يمكنك مشاهدة بث القناة ' => 'حيث يمكنك مشاهدة بث ', // Arabic
                '漢語 & 外部連結'                   => '漢語 外部連結', // Chinese
                'Диалектные группы'           => 'диалектные группы', // Russian
                '  ąśżź ćęłó   '              => 'ąśżź ćęłó', // Polish
                'à'                          => 'à', // Letter with combining grave accent (U+0061 U+0300)
                'ö́'                         => 'ö́', // Letter with combining diaeresis and acute accent (U+006F U+0308 U+0301)
                'Schalke\'05$ @++ !'          => 'schalke05', // Punctuation, Digits
                '0, ১, ২, ৩, ৪'               => '0 ১ ২ ৩ ৪', // Indian numbers
            ) as $tagUser => $tagExpected
        ) {
            $video->getTags()->add($tagUser);
            $this->assertSame(array($tagExpected), $video->getTags()->get());
            $video->getTags()->delete($tagExpected);
        }
    }

    public function testAddDuplicates() {
        $video = SKTest_TH::createVideo();
        $video->getTags()->add(array('bar', 'foo'));
        $this->assertSame(array('bar', 'foo'), $video->getTags()->get());

        $video->getTags()->add(array('foobar'));
        $this->assertSame(array('bar', 'foo', 'foobar'), $video->getTags()->get());

        $video->getTags()->add(array('foo', 'barfoo'));
        $this->assertSame(array('bar', 'barfoo', 'foo', 'foobar'), $video->getTags()->get());
    }
}
