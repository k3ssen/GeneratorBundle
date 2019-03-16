<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

class CrudCommandTest extends AbstractCommandTest
{
    public function testGenerateEntitiesWithRelationships()
    {
        $this->generateEntityAndAssertCommand('Library');
        // restart kernel, to make sure entity manager will be refreshed (otherwise the Library entity won't be found)
        static::ensureKernelShutdown();
        static::bootKernel();
        $this->generateCrudAndAssertCommand('Library');

        $this->assertControllerMatchesFile('Library', null, 'Admin');
        $this->assertDatatableMatchesFile('Library');
        $this->assertFormMatchesFile('Library');
        $this->assertVoterMatchesFile('Library');

        $this->assertTemplateMatchesFile('admin/library', 'delete');
        $this->assertTemplateMatchesFile('admin/library', 'edit');
        $this->assertTemplateMatchesFile('admin/library', 'index');
        $this->assertTemplateMatchesFile('admin/library', 'show');
        $this->assertTemplateMatchesFile('admin/library', 'new');
    }

    protected function generateCrudAndAssertCommand(string $fileName)
    {
        $this->generateAndAssertCommand('generator:crud', 'Crud', $fileName);
    }
}