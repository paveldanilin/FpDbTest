<?php

namespace FpDbTest\Template\Cache;

use FpDbTest\Template\TemplateInterface;

interface CacheInterface
{
    public function get(string $hash): ?TemplateInterface;
    public function put(string $hash, TemplateInterface $template);
}
