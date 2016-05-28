<?php

namespace ElmarHinz\TypoScript\Tests\Unit;

use \ElmarHinz\TypoScript\Tests\Unit\Fixtures\TypoScriptExamples as Examples;
use \ElmarHinz\TypoScript\TypoScriptParser as Parser;

class TypoScriptParserTest extends \PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$modifierClass = '\\ElmarHinz\\TypoScript\\ValueModifierInterface';
		$modifier = $this->getMockBuilder($modifierClass)->getMock();
		$modifier->method('modifyValue')->willReturn(
			'pre_value');
		$this->parser = new Parser();
		$this->parser->setValueModifier($modifier);
	}

	public function tsProvider()
	{
		return Examples::getExamples();
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($input, $tree)
	{
		$this->parser->appendTemplate($input);
		$result = $this->parser->parse();
		$this->assertSame($tree, $result);
	}

}

