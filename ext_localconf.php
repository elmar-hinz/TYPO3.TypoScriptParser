<?php

if (TYPO3_MODE == 'FE') {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\TypoScript\\Parser\\TypoScriptParser'] = array(
			'className' => 'ElmarHinz\\TypoScript\\CoreTypoScriptParserAdapter',
		);
}

