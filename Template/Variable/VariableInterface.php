<?php

namespace FpDbTest\Template\Variable;

interface VariableInterface
{
    public function getPosition(): int;

    public function convert($value): string;
}
