<?php

namespace FpDbTest;

use FpDbTest\Template\QueryTemplate;
use FpDbTest\Template\TemplateInterface;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private array $queriesMap;
    private string $blockSkip;

    public function __construct(mysqli $mysqli, string $blockSkip = '@@')
    {
        $this->mysqli = $mysqli;
        $this->queriesMap = [];
        $this->blockSkip = $blockSkip;
    }

    public function buildQuery(string $query, array $args = []): string
    {
        return $this->getTemplate($query)->render($args);
    }

    public function skip()
    {
        return $this->blockSkip;
    }

    // TODO: should be an external factory
    private function getTemplate(string $query): TemplateInterface
    {
        $queryKey = md5($query);
        if (!array_key_exists($queryKey, $this->queriesMap)) {
            $this->queriesMap[$queryKey] = new QueryTemplate(
                $query,
                '?',
                '{',
                '}',
                $this->blockSkip);
        }

        return $this->queriesMap[$queryKey];
    }
}
