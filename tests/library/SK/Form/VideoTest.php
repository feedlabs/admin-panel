<?php

class SK_Form_VideoTest extends SKTest_TestCase {

    public function setUp() {
        $videoWhitelist = new SK_Paging_ContentList_Video();
        $videoWhitelist->add('www.youtube.com');
        $videoWhitelist->add('xhamster.com');
        $videoWhitelist->add('www.metacafe.com');
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testProcessAdd() {
        $user = SKTest_TH::createUser();
        $form = new SK_Form_Video();
        $formAction = new SK_FormAction_Video_Add($form);

        $data = [
            'video'       => null,
            'title'       => 'Test',
            'description' => 'More Test',
            'privacy'     => SK_ModelAsset_Entity_PrivacyAbstract::NONE,
            'tags'        => 'Test, MoreTest',
            'code'        => '<iframe width="560" height="315" src="http://www.youtube.com/embed/MtN1YnoL46Q" frameborder="0" allowfullscreen></iframe>',
        ];
        $request = $this->createRequestFormAction($formAction, $data);
        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseSuccess($response);

        /** @var SK_Entity_Video $video */
        $video = $user->getVideos()->getItem(0);
        $this->assertInstanceOf('SK_Entity_Video', $video);
        $this->assertEquals('Test', $video->getTitle());
        $this->assertContainsAll(array("test", "moretest"), $video->getTags()->get());
        $this->assertContains('More Test', $video->getDescription());
        $this->assertContains('.jpg', $video->getSceneFirst()->getThumbnailUrl());

        $data = array(
            'video'       => null,
            'title'       => 'Test',
            'description' => 'More Test',
            'privacy'     => SK_ModelAsset_Entity_PrivacyAbstract::NONE,
            'code'        => '<iframe width="510" height="400" src="http://xhamster.com/xembed.php?video=1756951" frameborder="0" scrolling="no"></iframe>',
        );
        $request = $this->createRequestFormAction($formAction, $data);
        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseSuccess($response);
        $video = $user->getVideos()->getItem(1);
        $this->assertContains('.jpg', $video->getSceneFirst()->getThumbnailUrl());

        $data = array(
            'video'       => null,
            'title'       => 'Test',
            'description' => 'More Test',
            'privacy'     => SK_ModelAsset_Entity_PrivacyAbstract::NONE,
            'tags'        => 'Test, MoreTest',
            'code'        => '<embed flashVars="playerVars=autoPlay=no" src="http://www.metacafe.com/fplayer/9943443/mark_hamill_talks_star_wars_episode_vii.swf" width="440" height="248" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_9943443" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed><div style="font-size:12px;"><a href="http://www.metacafe.com/watch/9943443/mark_hamill_talks_star_wars_episode_vii/">Mark Hamill Talks Star Wars: Episode VII</a> - <a href="http://www.metacafe.com/">Watch more funny videos here</a></div>',
        );
        $request = $this->createRequestFormAction($formAction, $data);
        $this->processRequestWithViewer($request, $user);
        $video = $user->getVideos()->getItem(2);
        $this->assertContains('.jpg', $video->getSceneFirst()->getThumbnailUrl());
    }

    public function testProcessEdit() {
        $user = SKTest_TH::createUser();
        $video = SKTest_TH::createVideo($user);

        $form = new SK_Form_Video();
        $formAction = new SK_FormAction_Video_Save($form);
        $data = array(
            'video'       => $video->getId(),
            'title'       => 'Test',
            'description' => 'More Test',
            'privacy'     => SK_ModelAsset_Entity_PrivacyAbstract::FRIENDSONLY,
        );
        $request = $this->createRequestFormAction($formAction, $data);

        $response = $this->processRequestWithViewer($request, $user);
        $this->assertFormResponseSuccess($response);
        SKTest_TH::reinstantiateModel($video);
        $this->assertEquals('Test', $video->getTitle());
        $this->assertContains('More Test', $video->getDescription());
    }
}
