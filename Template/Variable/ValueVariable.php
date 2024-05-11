<?php

namespace FpDbTest\Template\Variable;

class ValueVariable extends Variable
{
    public function convert($value): string
    {
        if (null === $value) {
            return 'NULL';
        }
        if (is_string($value)) {
            return "'" . addslashes($value) . "'";
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
