<?php

namespace FpDbTest\Template\Cache;

use FpDbTest\Template\TemplateInterface;

class PhpCache implements CacheInterface
{
    public function get(string $hash): ?TemplateInterface
    {
        // TODO: Implement get() method.
        return null;
    }

    public function put(string $hash, TemplateInterface $template)
    {
        // TODO: Implement put() method.
    }
}
