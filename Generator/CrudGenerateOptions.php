<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

class CrudGenerateOptions
{
    /** @var string|null */
    protected $defaultBundleNamespace;

    /** @var string|null */
    protected $templatesDirectory;

    /** @var string|null */
    protected $templatesFileExtension;

    /** @var bool */
    protected $askUseVoter;
    /** @var bool */
    protected $useVoterDefault;
    /** @var bool */
    protected $useVoter;
    /** @var bool */
    protected $checkSecurityBundleEnabled;

    /** @var bool */
    protected $askUseWriteActions;
    /** @var bool */
    protected $useWriteActionsDefault;
    /** @var bool */
    protected $useWriteActions;

    /** @var bool */
    protected $askUseDatatable;
    /** @var bool */
    protected $useDatatableDefault;
    /** @var bool */
    protected $useDatatable;
    /** @var bool */
    protected $checkSgDatatablesBundleEnabled;

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

    public function getCheckSecurityBundleEnabled(): ?bool
    {
        return $this->checkSecurityBundleEnabled;
    }

    /**
     * @required
     */
    public function setCheckSecurityBundleEnabled(bool $checkSecurityBundleEnabled)
    {
        $this->checkSecurityBundleEnabled = $checkSecurityBundleEnabled;
        return $this;
    }

    public function getUseVoterDefault(): bool
    {
        return $this->useVoterDefault;
    }

    /**
     * @required
     */
    public function setTemplatesDirectory(?string $templatesDirectory)
    {
        $this->templatesDirectory = $templatesDirectory;
        return $this;
    }

    public function getTemplatesDirectory(): ?string
    {
        return $this->templatesDirectory;
    }

    /**
     * @required
     */
    public function setTemplatesFileExtension(?string $templatesFileExtension)
    {
        $this->templatesFileExtension = $templatesFileExtension;
        return $this;
    }

    public function getTemplatesFileExtension(): ?string
    {
        return $this->templatesFileExtension;
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

    public function getAskUseDatatable(): bool
    {
        return $this->askUseDatatable;
    }

    /**
     * @required
     */
    public function setAskUseDatatable(bool $askUseDatatable): self
    {
        $this->askUseDatatable = $askUseDatatable;
        return $this;
    }

    public function getCheckSgDatatablesBundleEnabled(): bool
    {
        return $this->checkSgDatatablesBundleEnabled;
    }

    /**
     * @required
     */
    public function setCheckSgDatatablesBundleEnabled(bool $checkSgDatatablesBundleEnabled): self
    {
        $this->checkSgDatatablesBundleEnabled = $checkSgDatatablesBundleEnabled;
        return $this;
    }

    public function getUseDatatableDefault(): ?bool
    {
        return $this->useDatatableDefault;
    }

    /**
     * @required
     */
    public function setUseDatatableDefault(bool $useDatatableDefault): self
    {
        $this->useDatatableDefault = $useDatatableDefault;
        return $this;
    }

    public function getUseDatatable(): bool
    {
        return $this->useDatatable ?? $this->getUseDatatableDefault();
    }

    public function setUseDatatable(bool $useDatatable)
    {
        $this->useDatatable = $useDatatable;
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
