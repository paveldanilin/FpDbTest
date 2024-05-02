<?php

namespace FpDbTest\Template\Variable;

abstract class Variable implements VariableInterface
{
    private ?string $spec;
    private int $position;

    public function __construct(int $position)
    {
        $this->spec = null;
        $this->position = $position;
    }

    public function getSpec(): ?string
    {
        return $this->spec;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    protected function setSpec(?string $spec): void
    {
        $this->spec = $spec;
    }
}
