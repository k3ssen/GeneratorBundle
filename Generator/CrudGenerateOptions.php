<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

class CrudGenerateOptions
{
    /** @var string|null */
    protected $defaultBundleNamespace;

    /** @var bool */
    protected $askUseVoter;
    /** @var bool */
    protected $useVoterDefault;
    /** @var bool */
    protected $useVoter;

    /** @var bool */
    protected $askUseWriteActions;
    /** @var bool */
    protected $useWriteActionsDefault;
    /** @var bool */
    protected $useWriteActions;

    /** @var bool */
    protected $askControllerSubdirectory;
    /** @var string */
    protected $controllerSubdirectoryDefault;
    /** @var string */
    protected $controllerSubdirectory;
    // We need this var to keep track if the controllerSubDirectory has been set, since null is a valid option as well.
    protected $controllerSubdirectoryDefined = false;

    public function getAskUseVoter(): ?bool
    {
        return $this->askUseVoter;
    }

    /**
     * @required
     */
    public function setAskUseVoter(bool $askUseVoter)
    {
        $this->askUseVoter = $askUseVoter;
        return $this;
    }

    public function getUseVoterDefault(): bool
    {
        return $this->useVoterDefault;
    }

    /**
     * @required
     */
    public function setUseVoterDefault(bool $useVoterDefault)
    {
        $this->useVoterDefault = $useVoterDefault;
        return $this;
    }

    public function getUseVoter(): bool
    {
        return $this->useVoter ?? $this->getUseVoterDefault();
    }

    public function setUseVoter(bool $useVoter)
    {
        $this->useVoter = $useVoter;
        return $this;
    }

    public function getAskUseWriteActions(): ?bool
    {
        return $this->askUseWriteActions;
    }

    /**
     * @required
     */
    public function setAskUseWriteActions(bool $askUseWriteActions)
    {
        $this->askUseWriteActions = $askUseWriteActions;
        return $this;
    }

    public function getUseWriteActionsDefault(): ?bool
    {
        return $this->useWriteActionsDefault;
    }

    /**
     * @required
     */
    public function setUseWriteActionsDefault(bool $useWriteActionsDefault)
    {
        $this->useWriteActionsDefault = $useWriteActionsDefault;
        return $this;
    }

    public function getUseWriteActions(): ?bool
    {
        return $this->useWriteActions ?? $this->getUseWriteActionsDefault();
    }

    public function setUseWriteActions(bool $useWriteActions)
    {
        $this->useWriteActions = $useWriteActions;
        return $this;
    }

    public function getAskControllerSubdirectory(): ?bool
    {
        return $this->askControllerSubdirectory;
    }

    /**
     * @required
     */
    public function setAskControllerSubdirectory(bool $askControllerSubdirectory)
    {
        $this->askControllerSubdirectory = $askControllerSubdirectory;
        return $this;
    }

    public function getControllerSubdirectoryDefault(): ?string
    {
        return $this->controllerSubdirectoryDefault;
    }

    /**
     * @required
     */
    public function setControllerSubdirectoryDefault(?string $controllerSubdirectoryDefault)
    {
        $this->controllerSubdirectoryDefault = $controllerSubdirectoryDefault;
        return $this;
    }

    public function getControllerSubdirectory(): ?string
    {
        if ($this->controllerSubdirectoryDefined) {
            return $this->controllerSubdirectory;
        }
        return $this->getControllerSubdirectoryDefault();
    }

    public function setControllerSubdirectory(?string $controllerSubdirectory)
    {
        $this->controllerSubdirectory = $controllerSubdirectory;
        $this->controllerSubdirectoryDefined = true;
        return $this;
    }

    public function getDefaultBundleNamespace(): string
    {
        if (!$this->defaultBundleNamespace) {
            throw new \RuntimeException('Cannot call method getDefaultBundleNamespace before setDefaultBundleNamespace.');
        }
        return $this->defaultBundleNamespace;
    }

    public function setDefaultBundleNamespace(string $baseBundleNamespace)
    {
        $this->defaultBundleNamespace = $baseBundleNamespace;
        return $this;
    }
}
