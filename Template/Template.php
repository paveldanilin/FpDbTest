<?php

namespace FpDbTest\Template;

use FpDbTest\Template\Variable\VariableInterface;

class Template implements TemplateInterface
{
    private array $partials;

    public function __construct(array $partials)
    {
        $this->partials = $partials;
    }

    public function render(array $args): string
    {
        $result = [];
        $partialIndex = 0;

        foreach ($this->partials as $partial) {
            if ($partial instanceof VariableInterface) {
                $result[] = $partial->convert($args[$partialIndex]??null);
                $partialIndex++;
            } else {
                $result[] = $partial; // const
            }
        }

        return implode($result);
    }

}
