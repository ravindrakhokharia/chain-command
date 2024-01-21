<?php

namespace Lib\ChainBundle\Manager;

class ChainCommandManager
{
    private array $parents = [];
    private array $children = [];
    public function __construct(
        private readonly array $chains,
    ) {
        $this->processChain($chains);
    }

    public function isParentCommand(string $commandName): bool
    {
        return array_key_exists($commandName, $this->parents);
    }

    public function isChildCommand(string $commandName): bool
    {
        return array_key_exists($commandName, $this->children);
    }

    public function getChildCommand(string $commandName): array
    {
        if (!$this->isChildCommand($commandName)) {
            throw new \LogicException('no child command found');
        }
        return $this->children[$commandName];
    }

    public function getParentCommand(string $commandName): array
    {
        if (!$this->isParentCommand($commandName)) {
            throw new \LogicException('no parent command found');
        }
        return $this->parents[$commandName];
    }

    private function processChain(array $chains)
    {
        foreach($chains as $chain) {
            $parent = $chain['parent_command'];
            $children = $chain['children'];
            $this->parents[$parent] = $children;
            foreach ($children as $child) {
                $this->children[$child['command']] = ['parent' => $parent, 'args' => $child['args']];
            }
        }

    }
}
