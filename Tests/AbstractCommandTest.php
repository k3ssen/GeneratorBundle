<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends WebTestCase
{
    public function setUp(): void
    {
        $this->cleanup();
        static::bootKernel();
    }

    public function tearDown()
    {
        parent::tearDown();
        // TODO: uncomment line below; currently disabled for development: it's much easier to see actually results.
//        $this->cleanup();
    }

    protected function cleanup()
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
    }

    protected function assertFileMatchesExpectedResult(string $directory, string $className, string $expectedResultFileName = null)
    {
        $expectedResultFileName = $expectedResultFileName ?? $className;
        $this->assertFileEquals(
            __DIR__.'/ExpectedResults/'.$directory.'/'.$expectedResultFileName.'.txt',
            __DIR__.'/App/'.$directory.'/'.$className.'.php'
        );
    }

    protected function assertEntityMatchesFile(string $className, string $expectedResultFileName = null, $subdirectory = null)
    {
        $directory = $subdirectory ? $subdirectory.'/Entity' : 'Entity';
        $this->assertFileMatchesExpectedResult($directory, $className, $expectedResultFileName);
    }

    protected function assertRepositoryMatchesFile(string $entityName, string $expectedResultFileName = null)
    {
        $this->assertFileMatchesExpectedResult('Repository', $entityName.'Repository', $expectedResultFileName);
    }

    protected function assertControllerMatchesFile(string $entityName, string $expectedResultFileName = null, $subdirectory = null)
    {
        $directory = $subdirectory ? $subdirectory.'/Controller' : 'Controller';
        $this->assertFileMatchesExpectedResult($directory, $entityName.'Controller', $expectedResultFileName);
    }

    protected function assertVoterMatchesFile(string $entityName, string $expectedResultFileName = null)
    {
        $this->assertFileMatchesExpectedResult('Security', $entityName.'Voter', $expectedResultFileName);
    }

    protected function assertFormMatchesFile(string $entityName, string $expectedResultFileName = null)
    {
        $this->assertFileMatchesExpectedResult('Form', $entityName.'Type', $expectedResultFileName);
    }

    protected function generateAndAssertCommand(string $commandName, string $commandSubdirectory, string $fileName)
    {
        $application = new Application(static::$kernel);

        $exampleOutputAndInput = file(__DIR__.'/ExampleCommandInputs/'.$commandSubdirectory.'/'.$fileName.'.txt');

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

        $command = $application->find($commandName);
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
    }
}