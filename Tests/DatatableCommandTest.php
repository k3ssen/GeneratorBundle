<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

class DatatableCommandTest extends AbstractCommandTest
{
    public function testGenerateCommand()
    {
        $this->generateEntityAndAssertCommand('Library');
        // restart kernel, to make sure entity manager will be refreshed (otherwise the Library entity won't be found)
        static::ensureKernelShutdown();
        static::bootKernel();
        $this->generateDatatableAndAssertCommand('Library');
    }

    protected function generateDatatableAndAssertCommand(string $fileName)
    {
        $this->generateAndAssertCommand('generator:datatable', 'Datatable', $fileName.'Datatable');
    }
}