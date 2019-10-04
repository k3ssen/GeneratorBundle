<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

class FormCommandTest extends AbstractCommandTest
{
    public function testGenerateCommand()
    {
        $this->generateEntityAndAssertCommand('Library');
        // restart kernel, to make sure entity manager will be refreshed (otherwise the Library entity won't be found)
        static::ensureKernelShutdown();
        static::bootKernel();
        $this->generateFormAndAssertCommand('Library');
    }

    protected function generateFormAndAssertCommand(string $fileName)
    {
        $this->generateAndAssertCommand('generator:form', 'Form', $fileName.'Type');
    }
}