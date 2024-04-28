<?php

namespace FpDbTest\Template;

class QueryTemplate implements TemplateInterface
{
    private string $specSign = '?';
    private string $blockStartSign = '{';
    private string $blockEndSign = '}';
    private string $blockSkip = '@@';
    private array $partials = [];
    private int $variablesCount = 0;
    private array $stopChars = [' ', '$', '%', '^', '&', '@', '~', ',', ';', '(', ')', '{' , '}', '[', ']', '>', '=', '<', '+', '-', '*', '/', "\t"];

    public function __construct(string $template, string $specSign = '?', string $blockStartSign = '{', string $blockEndSign = '}', string $blockSkip = '@@')
    {
        if (empty(trim($template))) {
            throw new \Exception('Template text is empty');
        }

        if (strlen($specSign) !== 1) {
            throw new \Exception('Spec sign must be a single char');
        }

        $this->parse($template);
        $this->specSign = $specSign;
        $this->blockStartSign = $blockStartSign;
        $this->blockEndSign = $blockEndSign;
        $this->blockSkip = $blockSkip;
    }

    public function render(array $args): string
    {
        $result = [];
        $partialIndex = 0;

        foreach ($this->partials as $partial) {
            if ($partial instanceof TemplateVariableInterface) {
                $result[] = $partial->convert($args[$partialIndex]??null);
                $partialIndex++;
            } else {
                $result[] = $partial; // const
            }
        }

        return implode($result);
    }

    // ---------------------------------------------------------------
    // TODO: must be external factory, keep here just for simplicity
    private function parse(string $text): void
    {
        // This is a const text (without spec and blocks)
        if(!str_contains($text, $this->specSign) && !str_contains($text, $this->blockStartSign)) {
            $this->partials[] = $text;
            return;
        }

        $len = strlen($text);
        $cursor = 0;

        for ($pos = 0; $pos < $len; $pos++) {
            $ch = $text[$pos];

            if ($ch === $this->specSign) {
                $spec = ltrim($this->getSubstringUntil($text, $len, $pos, $this->stopChars), $this->specSign);

                $this->partials[] = substr($text, $cursor, $pos - $cursor);
                $pos = $cursor = $pos + strlen($spec) + 1; // '+ 1' - compensate spec char

                switch ($spec) {
                    case '#':
                        $this->partials[] = new IdentityVariable($pos);
                        $this->variablesCount++;
                        break;
                    case 'a':
                        $this->partials[] = new ArrayVariable($pos);
                        $this->variablesCount++;
                        break;
                    case 'd':
                        $this->partials[] = new IntVariable($pos);
                        $this->variablesCount++;
                        break;
                    case 'f':
                        $this->partials[] = new FloatVariable($pos);
                        $this->variablesCount++;
                        break;
                    default:
                        $this->partials[] = new ValueVariable($pos);
                        $this->variablesCount++;
                        break;
                }
            } elseif ($ch === $this->blockStartSign) {
                $block = $this->getBlock($text, $len, $pos,  $this->blockStartSign, $this->blockEndSign);
                $this->partials[] = substr($text, $cursor, $pos - $cursor);
                $pos = $cursor = $pos + strlen($block);

                $this->partials[] = new BlockVariable($pos, substr($block, 1, -1), $this->blockSkip);
                $this->variablesCount++;
            }
        }

        if ($cursor != $len) {
            $this->partials[] = substr($text, $cursor);
        }
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
    // ---------------------------------------------------------------
}
