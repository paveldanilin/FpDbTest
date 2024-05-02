<?php

namespace FpDbTest;

use FpDbTest\Template\DefaultTemplateFactory;
use FpDbTest\Template\TemplateFactoryInterface;
use FpDbTest\Template\Cache\MemoryCache;
use FpDbTest\Template\QueryCompiler;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private string $blockSkip;
    private TemplateFactoryInterface $templateFactory;

    public function __construct(mysqli $mysqli, string $blockSkip = '@@')
    {
        $this->mysqli = $mysqli;
        $this->blockSkip = $blockSkip;
        $this->templateFactory = new DefaultTemplateFactory(
            new QueryCompiler('?', '{', '}', $blockSkip),
            new MemoryCache());
    }

    public function buildQuery(string $query, array $args = []): string
    {
        return $this->templateFactory->create($query)->render($args);
    }

    public function skip()
    {
        return $this->blockSkip;
    }
}
