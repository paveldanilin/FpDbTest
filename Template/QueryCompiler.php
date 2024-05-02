<?php

namespace FpDbTest\Template;

use FpDbTest\Template\Variable\VariableInterface;
use FpDbTest\Template\Variable\IdentityVariable;
use FpDbTest\Template\Variable\ArrayVariable;
use FpDbTest\Template\Variable\IntVariable;
use FpDbTest\Template\Variable\FloatVariable;
use FpDbTest\Template\Variable\BlockVariable;
use FpDbTest\Template\Variable\ValueVariable;

class QueryCompiler implements CompilerInterface
{
    private string $specSign = '?';
    private array $varFactoryMap = [];
    private string $blockStartSign = '{';
    private string $blockEndSign = '}';
    private string $blockSkip = '@@';
    private int $variablesCount = 0;
    private array $stopChars = [
        ' ', '$', '%', '^', '&', '@', '~', ',',
        ';', '(', ')', '{' , '}', '[', ']', '>',
        '=', '<', '+', '-', '*', '/', "\t"];

    public function __construct(string $specSign = '?',
                                string $blockStartSign = '{',
                                string $blockEndSign = '}',
                                string $blockSkip = '@@')
    {
        if (strlen($specSign) !== 1) {
            throw new \Exception('Spec sign must be a single char');
        }

        $this->specSign = $specSign;
        $this->blockStartSign = $blockStartSign;
        $this->blockEndSign = $blockEndSign;
        $this->blockSkip = $blockSkip;
        // TODO:
        $this->varFactoryMap['#'] = function (int $pos): VariableInterface {
          return new IdentityVariable($pos);
        };
        $this->varFactoryMap['a'] = function (int $pos): VariableInterface {
            return new ArrayVariable($pos);
        };
        $this->varFactoryMap['d'] = function (int $pos): VariableInterface {
            return new IntVariable($pos);
        };
        $this->varFactoryMap['f'] = function (int $pos): VariableInterface {
            return new FloatVariable($pos);
        };
    }

    public function compile(string $template): TemplateInterface
    {
        if (empty(trim($template))) {
            throw new \Exception('Template text is empty');
        }

        $partials = [];

        // This is a const text (without spec and blocks)
        if(!str_contains($template, $this->specSign) && !str_contains($template, $this->blockStartSign)) {
            $partials[] = $template;
            return new QueryTemplate($partials);
        }

        $len = strlen($template);
        $cursor = 0;

        for ($pos = 0; $pos < $len; $pos++) {
            $ch = $template[$pos];

            if ($ch === $this->specSign) {
                $spec = ltrim($this->getSubstringUntil($template, $len, $pos, $this->stopChars), $this->specSign);

                $partials[] = substr($template, $cursor, $pos - $cursor);
                $pos = $cursor = $pos + strlen($spec) + 1; // '+ 1' - compensate spec char

                $partials[] = $this->createVariable($spec, $pos);
                $this->variablesCount++;
            } elseif ($ch === $this->blockStartSign) {
                $block = $this->getBlock($template, $len, $pos,  $this->blockStartSign, $this->blockEndSign);
                $partials[] = substr($template, $cursor, $pos - $cursor);
                $pos = $cursor = $pos + strlen($block);

                $partials[] = new BlockVariable($pos, $this->blockSkip, $this->compile(substr($block, 1, -1)));
                $this->variablesCount++;
            }
        }

        if ($cursor != $len) {
            $partials[] = substr($template, $cursor);
        }

        return new QueryTemplate($partials);
    }

    private function createVariable(string $spec, int $pos): VariableInterface
    {
        $var = $this->varFactoryMap[$spec] ?? new ValueVariable($pos);
        if (is_callable($var)) {
            return $var($pos);
        }
        return $var;
    }

    private function getSubstringUntil(string $str, int $len, int $start, array $until): string
    {
        if ($start >= $len) {
            return '';
        }

        $subString = '';

        for ($i = $start; $i < $len; $i++) {
            if (in_array($str[$i], $until)) {
                break;
            } else {
                $subString .= $str[$i];
            }
        }

        return $subString;
    }

    private function getBlock(string $str, int $len, int $start, string $blockStartSign, string $blockEndSign): string
    {
        if ($start >= $len) {
            return '';
        }

        $block = '';
        $nestedBlockCount = 0;

        for ($i = $start; $i < $len; $i++) {
            if ($str[$i] === $blockStartSign) {
                $nestedBlockCount++;
            } elseif ($str[$i] === $blockEndSign) {
                $nestedBlockCount--;
            }

            $block .= $str[$i];

            if ($str[$i] === $blockEndSign && $nestedBlockCount === 0) {
                break;
            }
        }

        if ($nestedBlockCount > 0) {
            throw new \Exception('Not found a closing block sign "' . $blockEndSign . '" at ' . $i);
        }

        return $block;
    }
}
