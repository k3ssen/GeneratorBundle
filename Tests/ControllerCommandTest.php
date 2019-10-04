<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

class ControllerCommandTest extends AbstractCommandTest
{
    public function testGenerateCommand()
    {
        $this->generateEntityAndAssertCommand('Library');
        // restart kernel, to make sure entity manager will be refreshed (otherwise the Library entity won't be found)
        static::ensureKernelShutdown();
        static::bootKernel();
        $this->generateControllerAndAssertCommand('Library');
        $this->assertControllerMatchesFile('Library');
    }

    protected function generateControllerAndAssertCommand(string $fileName)
    {
        $this->generateAndAssertCommand('generator:controller', 'Controller', $fileName.'Controller');
    }
}