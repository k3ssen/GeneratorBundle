<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Reader;

use K3ssen\GeneratorBundle\MetaData\MetaEntityInterface;

class BundleProvider
{
    /**
     * @var array
     */
    protected $bundlesMetaData;
    /**
     * @var string
     */
    private $defaultBundleName;
    /**
     * @var string
     */
    private $projectDir;

    public function __construct($bundlesMetaData, string $projectDir, ?string $defaultBundle)
    {
        $this->bundlesMetaData = $bundlesMetaData;
        $this->defaultBundleName = $defaultBundle;
        $this->projectDir = $projectDir;
    }

    public function getBundleNameOptions(): array
    {
        $options = [];
        foreach ($this->bundlesMetaData as $bundleName => $bundleData) {
            if (strpos($bundleData['path'], '/vendor/') !== false) {
                continue;
            }
            $options[] = $bundleName;
        }
        return $options;
    }

    public function getBundleNames(): array
    {
        return array_keys($this->bundlesMetaData);
    }

    public function getBundleNamespaceByName(?string $name): string
    {
        foreach ($this->bundlesMetaData as $bundleName => $bundleData) {
            if ($name === $bundleName) {
                return $bundleData['namespace'];
            }
        }
        return MetaEntityInterface::NO_BUNDLE_NAMESPACE;
    }

    public function getBundlePathByName(?string $name): string
    {
        foreach ($this->bundlesMetaData as $bundleName => $bundleData) {
            if ($name === $bundleName) {
                return $bundleData['path'];
            }
        }
        return $this->projectDir.'/src';
    }

    public function isEnabled(string $bundleName): bool
    {
        return array_key_exists($bundleName, $this->bundlesMetaData);
    }

    public function getDefaultBundleName(): ?string
    {
        return $this->defaultBundleName;
    }

    public function getDefaultBundleNamespace(): string
    {
        return $this->getBundleNamespaceByName($this->getDefaultBundleName());
    }

    public function getDefaultBundlePath(): string
    {
        return $this->getBundlePathByName($this->getDefaultBundleName());
    }
}