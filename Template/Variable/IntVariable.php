<?php

namespace FpDbTest\Template\Variable;

class IntVariable extends Variable
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
