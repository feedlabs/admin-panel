<?php

require_once CM_Util::getModulePath('SK') . 'library/SK/SmartyPlugins/function.indicator.php';

class smarty_function_IndicatorTest extends CMTest_TestCase {

    /** @var Smarty_Internal_Template */
    private $_template;

    public function setUp() {
        $smarty = new Smarty();
        $render = new CM_Frontend_Render();
        $this->_template = $smarty->createTemplate('string:');
        $this->_template->assignGlobal('render', $render);
    }

    public function testProcess() {
        $expected = '<span class="function-indicator nowrap foo" title="100k Views"><span class="icon icon-eye"></span>100k Views</span>';
        $actual = smarty_function_indicator(array(
            'value' => 100000,
            'icon'  => 'eye',
            'title' => '{$value} Views',
            'label' => 'Views',
            'class' => 'foo',
        ), $this->_template);

        $this->assertSame($expected, $actual);
    }

    public function testFormatSigned() {
        $expected = '<span class="function-indicator nowrap">+10</span>';
        $actual = smarty_function_indicator(array('value' => 10, 'format' => 'signed'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testFormatUnsigned() {
        $expected = '<span class="function-indicator nowrap">10</span>';
        $actual = smarty_function_indicator(array('value' => 10, 'format' => 'unsigned'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testFormatDuration() {
        $expected = '<span class="function-indicator nowrap"><span class="date-period">00:10</span></span>';
        $actual = smarty_function_indicator(array('value' => 10, 'format' => 'duration'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testNoArguments() {
        $expected = '<span class="function-indicator nowrap"></span>';
        $actual = smarty_function_indicator(array(), $this->_template);
        $this->assertSame($expected, $actual);
    }


    public function testTitle() {
        $expected = '<span class="function-indicator nowrap" title="+10 Hello">+10</span>';
        $actual = smarty_function_indicator(array('value' => 10, 'format' => 'signed', 'title' => 'Hello'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testTitleIncludingValue() {
        $expected = '<span class="function-indicator nowrap" title="Hello +10">+10</span>';
        $actual = smarty_function_indicator(array('value' => 10, 'format' => 'signed', 'title' => 'Hello {$value}'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testTitleNoValue() {
        $expected = '<span class="function-indicator nowrap" title="Hello"></span>';
        $actual = smarty_function_indicator(array('format' => 'signed', 'title' => 'Hello'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testTitleIncludingValueNoValue() {
        $expected = '<span class="function-indicator nowrap" title="Hello "></span>';
        $actual = smarty_function_indicator(array('format' => 'signed', 'title' => 'Hello {$value}'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testLabel() {
        $expected = '<span class="function-indicator nowrap">+10 Hello</span>';
        $actual = smarty_function_indicator(array('value' => 10, 'format' => 'signed', 'label' => 'Hello'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testLabelIncludingValue() {
        $expected = '<span class="function-indicator nowrap">Hello +10</span>';
        $actual = smarty_function_indicator(array('value' => 10, 'format' => 'signed', 'label' => 'Hello {$value}'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testLabelNoValue() {
        $expected = '<span class="function-indicator nowrap">Hello</span>';
        $actual = smarty_function_indicator(array('label' => 'Hello'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    public function testLabelIncludingValueNoValue() {
        $expected = '<span class="function-indicator nowrap">Hello </span>';
        $actual = smarty_function_indicator(array('label' => 'Hello {$value}'), $this->_template);
        $this->assertSame($expected, $actual);
    }

    /**
     * @expectedException CM_Exception_Invalid
     * @expectedExceptionMessage Invalid format
     */
    public function testFormatNotExists() {
        smarty_function_indicator(array('value' => 10, 'format' => 'notExists'), $this->_template);
    }
}
