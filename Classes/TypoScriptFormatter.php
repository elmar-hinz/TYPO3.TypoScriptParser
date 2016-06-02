<?php

namespace ElmarHinz\TypoScript;

/**
 * TypoScript syntax formatter
 *
 * Responsible for:
 *
 * - Error tracking
 * - Error formatting
 * - Token formatting
 * - Line formatting
 * - Final document formatting
 * - Line number tracking
 *
 * If a number for the first line is not set, the first line is 1.
 *
 * @see: TypoScriptFormatterInterface
 */
class TypoScriptFormatter implements TypoScriptFormatterInterface
{
	/**
	 * Formatter strings
	 */
	const COMPOSE_FORMAT = '<pre class="ts-hl">%s</pre>';
	const ELEMENT_FORMAT = '<span class="%s">%s</span>';
	const ERROR_FORMAT =
		' <span class="ts-error"><strong> - ERROR:</strong> %s</span>';
	const LINE_FORMAT = '%s%s%s';
	const LINE_NUMBER_FORMAT = '<span class="ts-linenum">%4d: </span>';

	/**
	 * CSS classes of highligthed elements and errors
	 */
	const COMMENT_CLASS            = 'ts-comment';
	const CONDITION_CLASS          = 'ts-condition';
	const DEFAULT_CLASS            = 'ts-default';
	const IGNORED_CLASS            = 'ts-ignored';
	const KEYS_CLASS               = 'ts-objstr';
	const KEYS_POSTSPACE_CLASS     = 'ts-objstr_postspace';
	const OPERATOR_CLASS           = 'ts-operator';
	const OPERATOR_POSTSPACE_CLASS = 'ts-operator_postspace';
	const PRESPACE_CLASS           = 'ts-prespace';
	const VALUE_CLASS              = 'ts-value';
	const VALUE_COPY_CLASS         = 'ts-value_copy';

	/**
	 * Token to class map
	 */
	protected $tokenToClassMap = [
		AbstractTypoScriptParser::COMMENT_CONTEXT_TOKEN
		=> self::COMMENT_CLASS,
		AbstractTypoScriptParser::COMMENT_TOKEN
		=> self::COMMENT_CLASS,
		AbstractTypoScriptParser::CONDITION_TOKEN
		=> self::CONDITION_CLASS,
		AbstractTypoScriptParser::IGNORED_TOKEN
		=> self::IGNORED_CLASS,
		AbstractTypoScriptParser::KEYS_POSTSPACE_TOKEN
		=> self::KEYS_POSTSPACE_CLASS,
		AbstractTypoScriptParser::KEYS_TOKEN
		=> self::KEYS_CLASS,
		AbstractTypoScriptParser::OPERATOR_POSTSPACE_TOKEN
		=> self::OPERATOR_POSTSPACE_CLASS,
		AbstractTypoScriptParser::OPERATOR_TOKEN
		=> self::OPERATOR_CLASS,
		AbstractTypoScriptParser::PRESPACE_TOKEN
		=> self::PRESPACE_CLASS,
		AbstractTypoScriptParser::VALUE_CONTEXT_TOKEN
		=> self::VALUE_CLASS,
		AbstractTypoScriptParser::VALUE_COPY_TOKEN
		=> self::VALUE_COPY_CLASS,
		AbstractTypoScriptParser::VALUE_TOKEN
		=> self::VALUE_CLASS,
	];

	/**
	 * Number of first line
	 */
	protected $numberOfFirstLine = 1;

	/**
	 * Line counter
	 */
	protected $lineCounter = 0;

	/**
	 * Collect the elements of the current line.
	 */
	protected $elementsOfCurrentLine = [];

	/**
	 * Collect the errors of the current line.
	 */
	protected $errorsOfCurrentLine = [];

	/**
	 * Collect the lines.
	 */
	protected $lines = [];

	/**
	 * Set number first line.
	 *
	 * If called, it shall be called before parsing.
	 *
	 * @param integer The line number.
	 * @return void
	 */
	public function setNumberOfFirstLine($number)
	{
		$this->numberOfFirstLine = $number;
	}

	/**
	 * Get the number of the last line.
	 *
	 * If lines start with 1 it is equal to the number of lines.
	 * To be called after parsing.
	 *
	 * @return integer The line number.
	 */
	public function getNumberOfLastLine()
	{
		return $this->numberOfFirstLine + $this->lineCounter - 1;
	}

	/**
	 * Get the number of lines.
	 *
	 * Count of all lines.
	 * To be called after parsing.
	 *
	 * @return integer The count of lines.
	 */
	public function getCountOfLines()
	{
		return $this->lineCounter;
	}

	public function pushToken($tokenClass, $element)
	{
		$class = $this->tokenToClassMap[$tokenClass];
		$format = self::ELEMENT_FORMAT;
		$element = htmlspecialchars($element);
		$this->elementsOfCurrentLine[] = sprintf($format, $class, $element);
	}

	public function pushError($message)
	{
		$this->errorsOfCurrentLine[] = $message;
	}

	public function finishLine()
	{
		$elements = '';
		$errors = '';
		if($this->elementsOfCurrentLine) {
			$elements = implode('', $this->elementsOfCurrentLine);
		}
		if($this->errorsOfCurrentLine) {
			$errors = implode('; ', $this->errorsOfCurrentLine);
			$this->highligthed .= sprintf(self::ERROR_FORMAT, $errors);
		}
		$nr = $this->numberOfFirstLine + $this->lineCounter;
		$nr = sprintf(self::LINE_NUMBER_FORMAT, $nr);
		$this->lines[] = sprintf(self::LINE_FORMAT, $nr, $elements, $errors);
		$this->elementsOfCurrentLine = [];
		$this->errorsOfCurrentLine = [];
		$this->lineCounter++;
	}

	public function finish()
	{
		return sprintf(self::COMPOSE_FORMAT, implode("\n", $this->lines));
	}

}

