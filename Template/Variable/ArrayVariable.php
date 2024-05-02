<?php

namespace FpDbTest\Template\Variable;

class ArrayVariable extends Variable
{
    public function convert($value): string
    {
        if (!is_array($value)) {
            throw new \Exception('value is not array for spec ?a at ' . $this->getPosition());
        }

        if (array_is_list($value)) {
            return implode(', ', $value);
        }

        $kvList = [];

        foreach ($value as $k => $v) {
            if (is_string($v)) {
                $v = "'$v'";
            } elseif (null === $v) {
                $v = 'NULL';
            }
            $kvList[] = "`$k` = $v";
        }

        return implode(', ', $kvList);
    }
}
