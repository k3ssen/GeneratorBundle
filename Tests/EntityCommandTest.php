<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class EntityCommandTest extends WebTestCase
{
    public function setUp(): void
    {
        // Remove directories and their generated files to make sure these do not mess with the new tests
        $cleanupDirNames = ['Repository', 'Voter', 'Entity', 'Form', 'templates'];
        foreach ($cleanupDirNames as $cleanupDirName) {
            if (is_dir($dir = __DIR__ . '/App/' . $cleanupDirName)) {
                array_map('unlink', glob("$dir/*.*"));
                rmdir($dir);
            }
        }
        // Entity-dir must exist or locators for entity will fail (doctrine functionality; cannot be changed)
        mkdir(__DIR__.'/App/Entity');
        self::bootKernel();
    }
    
    public function testGeneratedResultForExampleMetaEntity()
    {
        $this->assertExampleEntity('Library');
    }

    protected function assertExampleEntity(string $entityName)
    {
        $application = new Application(static::$kernel);

        $exampleOutputAndInput = file(__DIR__.'/ExampleCommandInputs/Entity/'.$entityName.'.txt');

        $inputAnswers = [];
        $expectedOutputs = [];
        foreach ($exampleOutputAndInput as $lineNr => $line) {
            $line = trim($line);
            // lines that start with '>' are those where answers are provided.
            if (strpos($line, '>') === 0) {
                $input = str_replace('>', '', $line);
                $inputAnswers[] = trim($input);
                // If a line start with a '#', then consider this a comment in the file that can be ignored.
            } elseif(strpos($line, '#') !== 0) {
                $expectedOutputs[] = trim($line);
            }
        }

        $command = $application->find('generator:entity:create');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs($inputAnswers);
        try {
            $commandTester->execute([
                'command'  => $command->getName(),
            ]);
        } catch (\Throwable $e) {
            print_r($e->getMessage());
            // do nothing if exceptions occur. This way, we allow '^C' to terminate a command while still being able to check the result.
        }

        $output = $commandTester->getDisplay(true);

        foreach (array_unique($expectedOutputs) as $expectedOutput) {
            $this->assertContains($expectedOutput, $output);
        }

        $this->assertFileEquals(__DIR__.'/App/Entity/'.$entityName.'.php', __DIR__.'/ExpectedResults/Entity/'.$entityName.'.txt');
    }
}