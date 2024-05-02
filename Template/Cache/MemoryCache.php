<?php

namespace FpDbTest\Template\Cache;

use FpDbTest\Template\TemplateInterface;

class MemoryCache implements CacheInterface
{
    private array $templates = [];

    public function get(string $hash): ?TemplateInterface
    {
        return $this->templates[$hash] ?? null;
    }

    public function put(string $hash, TemplateInterface $template)
    {
        $this->templates[$hash] = $template;
    }
}
