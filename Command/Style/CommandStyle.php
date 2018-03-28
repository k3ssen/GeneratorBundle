<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Command\Style;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandStyle extends SymfonyStyle
{
    public function askQuestion(Question $question)
    {
        $answer = parent::askQuestion($question);
        return $answer === 'null' || $answer === '~' ? null : $answer;
    }

    public function ask($question, $default = null, $validator = null)
    {
        $answer = parent::ask($question, $default, $validator);
        return $answer === 'null' || $answer === '~' ? null : $answer;
    }

    public function header($text)
    {
        $this->block($text, null, 'options=bold;fg=cyan;bg=blue', '    ', true);
    }

    public function listing(array $options)
    {
        if (count($options)) {
            $this->writeln(sprintf(' <info>Available options:</info> <comment>%s</comment>', implode('</comment>, <comment>', $options)));
        }
    }
}
