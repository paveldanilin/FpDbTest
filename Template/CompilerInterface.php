<?php

namespace FpDbTest\Template;

interface CompilerInterface
{
    public function compile(string $template): TemplateInterface;
}
