<?php
declare(strict_types=1);

namespace K3ssen\GeneratorBundle\Generator;

class CrudGenerateOptions
{
    protected $usingVoters = false;
    protected $usingWriteActions = false;
    protected $usingDatatable = false;

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

    public function isUsingDatatable(): bool
    {
        return $this->usingDatatable;
    }

    public function setUsingDatatable(bool $usingDatatable)
    {
        $this->usingDatatable = $usingDatatable;
    }
}
