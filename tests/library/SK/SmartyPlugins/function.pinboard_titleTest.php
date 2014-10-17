<?php

require_once CM_Util::getModulePath('SK') . 'library/SK/SmartyPlugins/function.pinboard_title.php';

class smarty_function_pinboard_titleTest extends CMTest_TestCase {

    /** @var Smarty_Internal_Template */
    private $_template;

    /** @var SK_User */
    private $_user;

    public function setUp() {
        $smarty = new Smarty();
        $render = new CM_Frontend_Render();
        $this->_template = $smarty->createTemplate('string:');
        $this->_template->assignGlobal('render', $render);
        $this->_user = SKTest_TH::createUser(null, 'njam');
    }

    public function tearDown() {
        SKTest_TH::clearEnv();
    }

    public function testTranslate() {
        $pinboard = SKTest_TH::createPinboard($this->_user, 'Pinboard');

        $expected = 'njam\'s Pinboard';
        $actual = smarty_function_pinboard_title(array('pinboard' => $pinboard), $this->_template);

        $this->assertSame($expected, $actual);
    }

    public function testPinboardName() {
        $pinboard = SKTest_TH::createPinboard($this->_user, 'foo');

        $expected = 'foo';
        $actual = smarty_function_pinboard_title(array('pinboard' => $pinboard), $this->_template);

        $this->assertSame($expected, $actual);
    }

    /**
     * @expectedException CM_Exception_InvalidParam
     * @expectedExceptionMessage $pinboard must be an instance of `SK_Entity_Pinboard`
     */
    public function testWrongParams() {
        smarty_function_pinboard_title(array('pinboard' => 'foo'), $this->_template);
    }
}
