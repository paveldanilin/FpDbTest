<?php

namespace FpDbTest\Template;

interface TemplateFactoryInterface
{
    public function create(string $template): TemplateInterface;
}
