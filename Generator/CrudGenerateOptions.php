<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

class CrudGenerateOptions
{
    protected $usingVoters = false;
    protected $usingWriteActions = false;
    protected $defaultBundleNamespace;
    /** @var string|null */
    protected $controllerSubdirectory = null;

    public function isUsingVoters(): bool
    {
        return $this->usingVoters;
    }

    public function setUsingVoters(bool $usingVoters)
    {
        $this->usingVoters = $usingVoters;
    }

    public function isUsingWriteActions(): bool
    {
        return $this->usingWriteActions;
    }

    public function setUsingWriteActions(bool $usingWriteActions)
    {
        $this->usingWriteActions = $usingWriteActions;
    }

    public function getDefaultBundleNamespace(): string
    {
        if (!$this->defaultBundleNamespace) {
            throw new \RuntimeException('No default bundle has been set yet for the GenerateOptions.');
        }
        return $this->defaultBundleNamespace;
    }

    public function setDefaultBundleNamespace(string $defaultBundleNamespace)
    {
        $this->defaultBundleNamespace = $defaultBundleNamespace;
    }

    public function getControllerSubdirectory()
    {
        return $this->controllerSubdirectory;
    }

    public function setControllerSubdirectory($controllerSubdirectory)
    {
        $this->controllerSubdirectory = $controllerSubdirectory;
    }
}
