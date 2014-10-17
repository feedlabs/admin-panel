<?php

class SK_FormField_EmbedvideoTest extends SKTest_TestCase {

    protected static $whiteList = array('www.youtube.com', 'player.vimeo.com', 'www.dailymotion.com', 'flashservice.xvideos.com', 'embed.redtube.com',
        'www.pornhub.com', 'xxxbunker.com', 'xhamster.com');

    public static function setUpBeforeClass() {
        $paging = new SK_Paging_ContentList_Video();
        foreach (self::$whiteList as $site) {
            $paging->add($site);
        }
    }

    public static function tearDownAfterClass() {
        $paging = new SK_Paging_ContentList_Video();
        foreach (self::$whiteList as $site) {
            $paging->remove($site);
        }
        parent::tearDownAfterClass();
    }

    /**
     * @dataProvider provider
     */
    public function testValidate($validationResultExpected, $userInput) {
        $field = new SK_FormField_Embedvideo();
        $environment = new CM_Frontend_Environment();
        $response = $this->getMockForAbstractClass('CM_Response_Abstract', array(), '', false);

        $validationResult = $field->validate($environment, $userInput);
        $this->assertEquals($validationResultExpected, $validationResult);
    }

    public function provider() {
        return array(
            array(
                array(
                    'type'         => 1,
                    'src'          => 'https://www.youtube.com/embed/OC4blr88jCw',
                    'ratio'        => 0.56,
                    'flashvars'    => null,
                    'thumbnailUrl' => 'https://img.youtube.com/vi/OC4blr88jCw/default.jpg'
                ),
                <<<'EOD'
        <iframe width="640" height="360" src="http://www.youtube.com/embed/OC4blr88jCw?feature=player_detailpage" frameborder="0" allowfullscreen></iframe>
EOD
            ),
            array(
                array(
                    'type'         => 1,
                    'src'          => 'https://www.youtube.com/embed/nZjAw267jWo',
                    'ratio'        => 0.56,
                    'flashvars'    => null,
                    'thumbnailUrl' => 'https://img.youtube.com/vi/nZjAw267jWo/default.jpg'
                ),
                <<<'EOD'
        <iframe width="640" height="360" src="//www.youtube.com/embed/nZjAw267jWo" frameborder="0" allowfullscreen></iframe>
EOD
            ),
            array(
                array(
                    'type'         => 1,
                    'src'          => 'https://player.vimeo.com/video/25989030',
                    'ratio'        => 1.78,
                    'flashvars'    => null,
                    'thumbnailUrl' => null
                ),
                <<<'EOD'
        <iframe src="http://player.vimeo.com/video/25989030" width="500" height="889" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe> <p><a href="http://vimeo.com/25989030">Bunny Rabbit Love</a> from <a href="http://vimeo.com/user5775581">Lisa Cat D</a> on <a href="http://vimeo.com">Vimeo</a>.</p>
EOD
            ),
            array(
                array(
                    'type'         => 1,
                    'src'          => 'https://www.dailymotion.com/embed/video/x80ft5',
                    'ratio'        => 0.75,
                    'flashvars'    => null,
                    'thumbnailUrl' => null
                ),
                <<<'EOD'
        <iframe frameborder="0" width="480" height="360" src="http://www.dailymotion.com/embed/video/x80ft5"></iframe><br /><a href="http://www.dailymotion.com/video/x80ft5_a-cat-mum-of-a-rabbit_animals" target="_blank">a cat mum of a rabbit</a> <i>par <a href="http://www.dailymotion.com/aoofy" target="_blank">aoofy</a></i>
EOD
            ),
            array(
                array(
                    'type'         => 1,
                    'src'          => 'http://flashservice.xvideos.com/embedframe/1581088',
                    'ratio'        => 0.78,
                    'flashvars'    => null,
                    'thumbnailUrl' => null
                ),
                <<<'EOD'
        <iframe src="http://flashservice.xvideos.com/embedframe/1581088" frameborder=0 width=510 height=400 scrolling=no></iframe>
EOD
            ),
            array(
                array(
                    'type'         => 2,
                    'src'          => 'http://embed.redtube.com/player/?id=57236&style=redtube',
                    'ratio'        => 0.79,
                    'flashvars'    => 'autostart=false',
                    'thumbnailUrl' => null
                ),
                <<<'EOD'
        <object height="344" width="434"><param name="allowfullscreen" value="true"><param name="AllowScriptAccess" value="always"><param name="movie" value="http://embed.redtube.com/player/"><param name="FlashVars" value="id=57236&style=redtube&autostart=false"><embed src="http://embed.redtube.com/player/?id=57236&style=redtube" allowfullscreen="true" AllowScriptAccess="always" flashvars="autostart=false" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" height="344" width="434" /></object>
EOD
            ),
            array(
                array(
                    'type'         => 1,
                    'src'          => 'http://www.pornhub.com/embed/1040190807',
                    'ratio'        => 0.79,
                    'flashvars'    => null,
                    'thumbnailUrl' => null
                ),
                <<<'EOD'
        <iframe src="http://www.pornhub.com/embed/1040190807" frameborder="0" width="608" height="481" scrolling="no"><a href="http://www.pornhub.com/view_video.php?viewkey=1040190807">Playful Pussy Cat</a> brought to you by <a href="http://www.pornhub.com/">PornHub</a></iframe><br /><a href="http://www.pornhub.com/view_video.php?viewkey=1040190807">Playful Pussy Cat</a> brought to you by <a href="http://www.pornhub.com/">PornHub</a>
EOD
            ),
            array(
                array(
                    'type'         => 2,
                    'src'          => 'http://xxxbunker.com/flash/player.swf',
                    'ratio'        => 0.73,
                    'flashvars'    => 'config=http%3A%2F%2Fxxxbunker.com%2FplayerConfig.php%3Fvideoid%3D1815929%26autoplay%3Dfalse',
                    'thumbnailUrl' => null
                ),
                <<<'EOD'
        <div style="text-align:center"><object width="550" height="400"><param name="movie" value="http://xxxbunker.com/flash/player.swf"></param><param name="wmode" value="transparent"></param><param name="allowfullscreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="flashvars" value="config=http%3A%2F%2Fxxxbunker.com%2FplayerConfig.php%3Fvideoid%3D1815929%26autoplay%3Dfalse"></param><embed src="http://xxxbunker.com/flash/player.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="transparent" width="550" height="400" flashvars="config=http%3A%2F%2Fxxxbunker.com%2FplayerConfig.php%3Fvideoid%3D1815929%26autoplay%3Dfalse"></embed></object><br/><strong style="font-size:12px"><a href="http://xxxbunker.com/flexible_cat_drilled_in_her_cunt" title="flexible cat drilled in her cunt">flexible cat drilled in her cunt</a><br/>Embedded from <a href="http://xxxbunker.com" title="xxxbunker.com : the worlds largest xxx tube site">xxxbunker.com : the worlds largest xxx tube site</a></strong></div>
EOD
            ),
            array(
                array(
                    'type'         => 1,
                    'src'          => 'https://xhamster.com/xembed.php?video=1721013',
                    'ratio'        => 0.78,
                    'flashvars'    => null,
                    'thumbnailUrl' => 'http://et0.xhamster.com/t/013/5_1721013.jpg'
                ),
                <<<'EOD'
        <iframe width="510" height="400" src="http://xhamster.com/xembed.php?video=1721013" frameborder="0" scrolling="no"></iframe>
EOD
            ),
        );
    }
}
