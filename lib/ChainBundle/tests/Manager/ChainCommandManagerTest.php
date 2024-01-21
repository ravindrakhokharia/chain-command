<?php

namespace Lib\ChainBundle\Tests\Manager;

use Lib\ChainBundle\Manager\ChainCommandManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Class ChainCommandManagerTest.
 *
 * @covers \Lib\ChainBundle\Manager\ChainCommandManager
 */
final class ChainCommandManagerTest extends TestCase
{
    private ChainCommandManager $chainCommandManager;

    private array $chains;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->chains = [
            [
                "parent_command" => "foo:hello",
                "children" => [
                    [
                        "command" => "bar:hi",
                        "args" => []
                    ]
                ]
            ]
        ];
        $this->chainCommandManager = new ChainCommandManager($this->chains);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->chainCommandManager);
        unset($this->chains);
    }

    /**
     * @dataProvider isParentCommandProvider
     */
    public function testIsParentCommand($commandName, $expected): void
    {
        $this->assertSame(
            $expected,
            $this->chainCommandManager->isParentCommand($commandName)
        );
    }

    /**
     * @dataProvider isChildCommandProvider
     */
    public function testIsChildCommand($commandName, $expected): void
    {
        $this->assertSame(
            $expected,
            $this->chainCommandManager->isChildCommand($commandName)
        );
    }

    public function testGetChildCommand(): void
    {
        $this->assertSame(
            ['parent' => 'foo:hello', 'args' => []],
            $this->chainCommandManager->getChildCommand('bar:hi')
        );
    }

    public function testGetParentCommand(): void
    {
        $this->assertSame(
            [
                [
                    "command" => "bar:hi",
                    "args" => []
                ]
            ],
            $this->chainCommandManager->getParentCommand('foo:hello')
        );
    }

    public static function isParentCommandProvider(): array
    {
        return [
            ['foo:hello', true],
            ['test:test', false],
            ['bar:hi', false],
        ];
    }

    public static function isChildCommandProvider(): array
    {
        return [
            ['foo:hello', false],
            ['test:test', false],
            ['bar:hi', true],
        ];
    }
}
