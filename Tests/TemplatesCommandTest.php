<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

class TemplatesCommandTest extends AbstractCommandTest
{
    public function testGenerateCommand()
    {
        $this->generateEntityAndAssertCommand('Library');
        // restart kernel, to make sure entity manager will be refreshed (otherwise the Library entity won't be found)
        static::ensureKernelShutdown();
        static::bootKernel();
        $this->generateTemplatesAndAssertCommand('Library');

        $this->assertTemplateMatchesFile('admin/library', 'delete');
        $this->assertTemplateMatchesFile('admin/library', 'edit');
        $this->assertTemplateMatchesFile('admin/library', 'index');
        $this->assertTemplateMatchesFile('admin/library', 'show');
        $this->assertTemplateMatchesFile('admin/library', 'new');
    }

    protected function generateTemplatesAndAssertCommand(string $fileName)
    {
        $this->generateAndAssertCommand('generator:templates', 'templates', $fileName);
    }
}