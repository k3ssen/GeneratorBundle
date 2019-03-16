<?php
declare(strict_types=1);

namespace App;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class GeneratorTestKernel extends Kernel
{
    public function getProjectDir()
    {
        return __DIR__ . '/../';
    }

    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Sg\DatatablesBundle\SgDatatablesBundle(),
            new \K3ssen\GeneratorBundle\GeneratorBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../config/config.yml');
    }
}