<?php

namespace FpDbTest\Template;

interface TemplateVariableInterface
{
    public function getPosition(): int;

    public function convert($value): string;
}
