<?php

namespace FpDbTest\Template;

interface TemplateInterface
{
    public function render(array $args): string;
}
