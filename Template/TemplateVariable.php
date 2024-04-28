<?php

namespace FpDbTest\Template;

abstract class TemplateVariable implements TemplateVariableInterface
{

    private int $position;

    public function __construct(int $position)
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
