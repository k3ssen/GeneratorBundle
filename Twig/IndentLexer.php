<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Twig;

use Twig_Source;

class IndentLexer extends \Twig_Lexer
{
    public function tokenize(Twig_Source $source)
    {
        $sourceCode = str_replace(array("\r\n", "\r"), "\n", $source->getCode());

        //Here we remove all spaces (but no newlines) before {% and {# and {{
        $sourceCode = preg_replace("/\n(    )*{(%|{|#)([^\+])/", "\n{\$2$3", $sourceCode);
        //We make exceptions for space-removal if the {% or {# or {{ is preceded by a +, but the + needs to be removed afterwards
        $sourceCode = preg_replace("/{(%|{|#)([\+])/", "{\$1", $sourceCode);

        $source = new Twig_Source($sourceCode, $source->getName(), $source->getPath());

        return parent::tokenize($source);
    }
}
