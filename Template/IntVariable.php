<?php

namespace FpDbTest\Template;

class IntVariable extends TemplateVariable
{
    public function convert($value): string
    {
        if (null === $value) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return (string)((int)$value);
        }
        if (is_numeric($value)) {
            return (string)((int)$value);
        }
        throw new \Exception('Unexpected value for "?d" at ' . $this->getPosition());
    }
}
