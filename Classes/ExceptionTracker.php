<?php

namespace ElmarHinz\TypoScriptParser;

use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptException;

class ExceptionTracker
{

    protected $lineExceptions = [];
    protected $templateExceptions = [];

    public function push(TypoScriptException $exception)
    {
        if($exception->isEndOfTemplateException()) {
            $this->templateExceptions[] = $exception;
        } else {
            $this->lineExceptions[$exception->getTemplateLineNumber()][]
                = $exception;
        }
    }

    public function getEndOfTemplateExceptions()
    {
        return $this->templateExceptions;
    }

    public function getExceptionsOfLine($lineNumber)
    {
        if(array_key_exists($lineNumber, $this->lineExceptions)) {
            return $this->lineExceptions[$lineNumber];
        } else {
            return [];
        }
    }

}

