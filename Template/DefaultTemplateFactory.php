<?php

namespace FpDbTest\Template;

use FpDbTest\Template\Cache\CacheInterface;

class DefaultTemplateFactory implements TemplateFactoryInterface
{
    private CompilerInterface $compiler;
    private ?CacheInterface $cache;

    public function __construct(CompilerInterface $compiler, ?CacheInterface $cache)
    {
        $this->compiler = $compiler;
        $this->cache = $cache;
    }

    public function create(string $template): TemplateInterface
    {
        // TODO: normalize template

        if (null === $this->cache) {
            return $this->compiler->compile($template);
        }

        $hash = $this->hash($template);
        $cachedTemplate = $this->cache->get($hash);
        if (null === $cachedTemplate) {
            $newTemplate = $this->compiler->compile($template);
            $this->cache->put($hash, $newTemplate);
            return $newTemplate;
        }

        return $cachedTemplate;
    }

    private function hash(string $template): string
    {
        return md5($template);
    }
}
