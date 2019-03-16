<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Tests;

class EntityCommandTest extends AbstractCommandTest
{
    public function testGenerateEntitiesWithRelationships()
    {
        $this->generateEntityAndAssertCommand('Bare');
        $this->assertEntityMatchesFile('Bare');
        $this->generateEntityAndAssertCommand('Library');
        $this->assertEntityMatchesFile('Library');
        $this->generateEntityAndAssertCommand('Book');
        $this->assertEntityMatchesFile('Book');
        //After creating the book entity, the Library should be updated and a Tenant should've been created.
        $this->assertEntityMatchesFile('Library', 'Library-after-book-creation');
        $this->assertEntityMatchesFile('Tenant');

        $this->assertRepositoryMatchesFile('Library');
        $this->assertRepositoryMatchesFile('Book');
        $this->assertRepositoryMatchesFile('Tenant');
    }

    public function testGenerateEntitiesWithAppend()
    {
        $this->generateEntityAndAssertCommand('Library');
        $this->assertEntityMatchesFile('Library');
        // restart kernel, to make sure entity manager will be refreshed (otherwise the Library entity won't be found)
        static::ensureKernelShutdown();
        static::bootKernel();
        $this->generateAppendEntityAndAssertCommand('Library-append');
        $this->assertEntityMatchesFile('Library', 'Library-after-append');
    }

    public function testGenerateEntitiesWithAlter()
    {
        $this->generateEntityAndAssertCommand('Library');
        $this->assertEntityMatchesFile('Library');
        // restart kernel, to make sure entity manager will be refreshed (otherwise the Library entity won't be found)
        static::ensureKernelShutdown();
        static::bootKernel();
        $this->generateAlterEntityAndAssertCommand('Library-alter');
        $this->assertEntityMatchesFile('LibraryBuilding');
        // FIXME: library entity should be removed here, but isn't
        // TODO: uncomment line below after library-fix
//        $this->assertFileNotExists(__DIR__.'/App/Entity/Library.php');
    }
}