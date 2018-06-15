<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

class EntityGenerateOptions
{
    /** @var string */
    protected $projectDir;
    /** @var array */
    protected $traitOptions;
    /** @var bool */
    protected $askBundle;
    /** @var bool */
    protected $askTraits;
    /** @var bool */
    protected $askDisplayField;
    /** @var bool */
    protected $useCustomRepository;
    /** @var bool */
    protected $askEntitySubdirectory;
    /** @var string|null */
    protected $defaultEntitySubdirectory;

    public function getProjectDir(): ?string
    {
        return $this->projectDir;
    }

    /**
     * @required
     */
    public function setProjectDir(string $projectDir)
    {
        $this->projectDir = $projectDir;
        return $this;
    }

    public function getTraitOptions(): ?array
    {
        return $this->traitOptions;
    }

    /**
     * @required
     */
    public function setTraitOptions(array $traitOptions)
    {
        $this->traitOptions = $traitOptions;
        return $this;
    }

    public function getAskBundle(): bool
    {
        return $this->askBundle;
    }

    /**
     * @required
     */
    public function setAskBundle(bool $askBundle)
    {
        $this->askBundle = $askBundle;
        return $this;
    }

    public function getAskTraits(): bool
    {
        return $this->askTraits;
    }

    /**
     * @required
     */
    public function setAskTraits(bool $askTraits)
    {
        $this->askTraits = $askTraits;
        return $this;
    }

    public function getAskDisplayField(): bool
    {
        return $this->askDisplayField;
    }

    /**
     * @required
     */
    public function setAskDisplayField(bool $askDisplayField)
    {
        $this->askDisplayField = $askDisplayField;
        return $this;
    }

    public function getUseCustomRepository(): bool
    {
        return $this->useCustomRepository;
    }

    /**
     * @required
     */
    public function setUseCustomRepository(bool $useCustomRepository)
    {
        $this->useCustomRepository = $useCustomRepository;
        return $this;
    }

    public function getAskEntitySubdirectory(): bool
    {
        return $this->askEntitySubdirectory;
    }

    /**
     * @required
     */
    public function setAskEntitySubdirectory(bool $askEntitySubdirectory)
    {
        $this->askEntitySubdirectory = $askEntitySubdirectory;
        return $this;
    }

    public function getDefaultEntitySubdirectory(): ?string
    {
        return $this->defaultEntitySubdirectory;
    }

    /**
     * @required
     */
    public function setDefaultEntitySubdirectory(?string $defaultEntitySubdirectory)
    {
        $this->defaultEntitySubdirectory = $defaultEntitySubdirectory;
        return $this;
    }
}
