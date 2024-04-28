<?php

namespace FpDbTest\Template;

class ValueVariable extends TemplateVariable
{
    public function convert($value): string
    {
        if (null === $value) {
            return 'NULL';
        }
        if (is_string($value)) {
            return "'$value'";
        }
        if (is_bool($value)) {
            return (int)$value;
        }
        if (is_numeric($value)) {
            return (string)$value;
        }
        throw new \Exception('Unexpected value for "?" at ' . $this->getPosition());
    }
}
