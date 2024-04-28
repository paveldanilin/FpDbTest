<?php

namespace FpDbTest\Template;

class IdentityVariable extends TemplateVariable
{
    public function convert($value): string
    {
        if (null === $value) {
            throw new \Exception('Identifier must be a string or an array of string at ' . $this->getPosition());
        }
        if (is_array($value)) {
            return implode(', ', array_map(function ($e) {
                return "`$e`";
            }, $value));
        }
        return "`$value`";
    }
}
