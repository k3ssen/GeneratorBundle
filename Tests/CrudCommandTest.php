<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

class CrudCommandTest extends AbstractCommandTest
{
    public function testGenerateEntitiesWithRelationships()
    {
        $this->generateCrudAndAssertCommand('Library');
    }

    protected function generateCrudAndAssertCommand(string $fileName)
    {
        $this->generateAndAssertCommand('generator:crud', 'Crud', $fileName);
    }
}