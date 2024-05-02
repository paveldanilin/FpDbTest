<?php

namespace FpDbTest\Template\Variable;

// Supports nested blocks
use FpDbTest\Template\TemplateInterface;

class BlockVariable extends Variable
{
    private string $blockSkip;
    private TemplateInterface $blockTemplate;

    public function __construct(int $position, string $blockSkip, TemplateInterface $block) {
        parent::__construct($position);
        $this->blockSkip = $blockSkip;
        $this->blockTemplate = $block;
    }

    public function convert($value): string
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $v) {
            if ($this->blockSkip === $v) {
                return '';
            }
        }

        return $this->blockTemplate->render($value);
    }
}
