<?php

namespace Lib\ChainBundle\Manager;

/**
 * Class ChainCommandManager
 * 
 * Chain command manager for the chain bundle
 */
class ChainCommandManager
{
    /**
     * @var array
     */
    private array $parents = [];
    /**
     * @var array
     */
    private array $children = [];

    public function __construct(
        private readonly array $chains,
    ) {
        $this->processChain($chains);
    }

    /**
     * @param string $commandName
     * 
     * @return bool
     */
    public function isParentCommand(string $commandName): bool
    {
        return array_key_exists($commandName, $this->parents);
    }

    /**
     * @param string $commandName
     * 
     * @return bool
     */
    public function isChildCommand(string $commandName): bool
    {
        return array_key_exists($commandName, $this->children);
    }

    /**
     * @param string $commandName
     * 
     * @return array
     */
    public function getChildCommand(string $commandName): array
    {
        if (!$this->isChildCommand($commandName)) {
            throw new \LogicException('no child command found');
        }
        return $this->children[$commandName];
    }

    /**
     * @param string $commandName
     * 
     * @return array
     */
    public function getParentCommand(string $commandName): array
    {
        if (!$this->isParentCommand($commandName)) {
            throw new \LogicException('no parent command found');
        }
        return $this->parents[$commandName];
    }

    /**
     * @param array $chains
     * 
     * @return [type]
     */
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
