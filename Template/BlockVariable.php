<?php

namespace FpDbTest\Template;

// Supports nested blocks
class BlockVariable extends TemplateVariable
{
    private string $blockSkip;
    private TemplateInterface $subTemplate;

    public function __construct(int $position, string $text, string $blockSkip) {
        parent::__construct($position);
        $this->subTemplate = new QueryTemplate($text);
        $this->blockSkip = $blockSkip;
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

        return $this->subTemplate->render($value);
    }
}
