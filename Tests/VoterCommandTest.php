<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

class VoterCommandTest extends AbstractCommandTest
{
    public function testGenerateCommand()
    {
        $this->generateEntityAndAssertCommand('Library');
        // restart kernel, to make sure entity manager will be refreshed (otherwise the Library entity won't be found)
        static::ensureKernelShutdown();
        static::bootKernel();
        $this->generateVoterAndAssertCommand('Library');
        $this->assertVoterMatchesFile('Library');
    }

    protected function generateVoterAndAssertCommand(string $fileName)
    {
        $this->generateAndAssertCommand('generator:voter', 'Security', $fileName.'Voter');
    }
}