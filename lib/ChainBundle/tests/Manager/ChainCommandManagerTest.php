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
    /**
     * @var ChainCommandManager
     */
    private ChainCommandManager $chainCommandManager;

    /**
     * @var array
     */
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

    /**
     * @return void
     */
    public function testGetChildCommand(): void
    {
        $this->assertSame(
            ['parent' => 'foo:hello', 'args' => []],
            $this->chainCommandManager->getChildCommand('bar:hi')
        );
    }

    /**
     * @return void
     */
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

    /**
     * @return array
     */
    public static function isParentCommandProvider(): array
    {
        return [
            ['foo:hello', true],
            ['test:test', false],
            ['bar:hi', false],
        ];
    }

    /**
     * @return array
     */
    public static function isChildCommandProvider(): array
    {
        return [
            ['foo:hello', false],
            ['test:test', false],
            ['bar:hi', true],
        ];
    }
}
