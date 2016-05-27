<?php

require_once("vendor/autoload.php");

class TypoScriptPreProcessorTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$matchClass = '\\TYPO3\\CMS\\Backend\\Configuration\\TypoScript\\ConditionMatching\\ConditionMatcher';
		$matcher = $this->getMockBuilder($matchClass)->getMock();
		$matcher->method('match')->will($this->returnCallback(
			function($condition) { return $condition == '[TRUE]'; }));
		$this->parser = new \ElmarHinz\TypoScriptPreProcessor();
		$this->parser->setMatcher($matcher);
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($dirty, $clean)
	{
		$this->parser->appendTemplate($dirty);
		$result = implode("\n", $this->parser->parse());
		$this->assertEquals($clean, $result);
	}

	public function tsProvider()
	{
		return array (
			'simple' => [
				'show' . PHP_EOL,
				'show'
			],
			'true' => [
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'first' . PHP_EOL .
				'after'
			],
			'false' => [
				'before' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'after'
			],
			'true-true' => [
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'first' . PHP_EOL .
				'second' . PHP_EOL .
				'after'
			],
			'true-false' => [
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'first' . PHP_EOL .
				'after'
			],
			'false-true' => [
				'before' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'second' . PHP_EOL .
				'after'
			],
			'false-false' => [
				'before' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'after'
			],
			'true-else' => [
				'before' . PHP_EOL .
				'[TRUE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[ELSE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL  .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'first' . PHP_EOL .
				'after'
			],
			'false-else' => [
				'before' . PHP_EOL .
				'[FALSE]' . PHP_EOL .
					'first' . PHP_EOL .
				'[ELSE]' . PHP_EOL .
					'second' . PHP_EOL .
				'[GLOBAL]' . PHP_EOL  .
				'after' . PHP_EOL,

				'before' . PHP_EOL .
				'second' . PHP_EOL .
				'after'
			],
		);
	}

}
