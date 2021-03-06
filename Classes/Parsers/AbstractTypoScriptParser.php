<?php

namespace ElmarHinz\TypoScriptParser\Parsers;

use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptParserInterface;
use ElmarHinz\TypoScriptParser\Interfaces\
    TypoScriptParsetimeExceptionTrackerPushInterface as ExceptionTracker;
use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptTokenTrackerPushInterface
    as TokenTracker;

abstract class AbstractTypoScriptParser implements TypoScriptParserInterface
{

	/*******************************************************
	 * Constants
	 *******************************************************/

	const DOT = '.';
	const NL = "\n";
	const EMPTY_STRING = '';

	/*******************************************************
	 * Regular expressions to tokenize TypoScript
	 *******************************************************/

	const COMMENT_CONTEXT_CLOSE_REGEX = '|^(\s*)(\*/)(.*)$|';
	const COMMENT_CONTEXT_OPEN_REGEX = '|^(\s*)(/\*)(.*)$|';
	const COMMENT_REGEX = '/^(\s*)(#|\/[^\*])(.*)$/';
	const CONDITION_REGEX = '|^(\s*)(\[.*)$|';
	const LEVEL_CLOSE_REGEX = '|^(\s*)(})(.*)$|';
	const OPERATOR_REGEX = '/^(\s*)([[:alnum:].:\\\\_-]*[[:alnum:]:\\\\_-])(\s*)(:=|[=<>{(])(\s*)(.*)$/';
	const VALUE_CONTEXT_OPEN_REGEX = '/^(\s*)([[:alnum:].\\\\_-]*[[:alnum:]\\\\_-])(\s*)[(].*$/';
	const VALUE_CONTEXT_CLOSE_REGEX = '|^(\s*)(\))(.*)$|';
	const VOID_REGEX = '|^\s*$|';

	/*******************************************************
	 * Regular expressions to debug TypoSciript
	 *******************************************************/

    /* Valid Key:
     *
     * Terminated by:
     *
     * EOL
     * whitespace
     * valid operator
     */
    const VALID_KEY_REGEX = '/^(\s*)([[:alnum:].:\\\\_-]*[[:alnum:]:\\\\_-])((\s|:=|[=<>{(]).*)?$/';
    const VALID_OPERATOR_REGEX = '/(:=|[=<>{(])/';

	/*******************************************************
	 * TypoSciript operators
	 *******************************************************/

	const ASSIGN_OPERATOR = '=';
	const COPY_OPERATOR = '<';
	const LEVEL_OPEN_OPERATOR = '{';
	const MODIFY_OPERATOR = ':=';
	const UNSET_OPERATOR = '>';
	const VALUE_CONTEXT_OPEN_OPERATOR = '(';

	/*******************************************************
	 * TypoSciript multiline contexts
	 *******************************************************/

	const COMMENT_CONTEXT = 1;
	const DEFAULT_CONTEXT = 2;
	const VALUE_CONTEXT   = 3;

	/*******************************************************
	 * Instance variables
	 *******************************************************/

	/**
	 * The lines to parse.
	 */
	protected $inputLines = Null;
    protected $exceptionTracker = null;
    protected $tokenTracker = null;


	/*******************************************************
	 * Methods
	 *******************************************************/

	/**
	 * Join multiple templates before parsing them.
	 *
	 * The template may be a multiline text
	 * or a text that is alreay split into lines.
	 *
	 * @param mixed Multiline text or array of lines.
	 */
	public function appendTemplate($template)
	{
		if (!is_array($template)) {
			if(substr($template, -1) == "\n")
			   $template = substr($template, 0, -1);
			$template = explode("\n", $template);
            for($i = 0; $i < count($template); $i++) {
                if(substr($template[$i], -1) == "\r")
                    $template[$i] = substr($template[$i], 0, -1);
            }
		}
		if($this->inputLines == Null) {
			$this->inputLines = $template;
		} else {
			foreach ($template as $line)  $this->inputLines[] = $line;
		}
	}

	/**
	 * Inject the exectption tracker
	 *
     * @param ExceptionTracker $tracker The exception tracker.
	 * @return void
	 */
	public function injectExceptionTracker(ExceptionTracker $tracker)
	{
		$this->exceptionTracker = $tracker;
	}

	/**
	 * Inject the token tracker
	 *
     * @param TokenTracker $tracker The token tracker.
	 * @return void
	 */
	public function injectTokenTracker(TokenTracker $tracker)
	{
		$this->tokenTracker = $tracker;
	}

}

