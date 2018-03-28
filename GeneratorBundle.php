<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle;

use K3ssen\GeneratorBundle\DependencyInjection\Compiler\GeneratorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GeneratorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new GeneratorCompilerPass());
    }
}
