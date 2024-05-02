<?php

namespace FpDbTest\Template\Variable;

class FloatVariable extends Variable
{
    public function convert($value): string
    {
        if (null === $value) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return (string)((float)$value);
        }
        if (is_numeric($value)) {
            return (string)((float)$value);
        }
        throw new \Exception('Unexpected value for "?f" at ' . $this->getPosition());
    }
}
