<?php

namespace Tests\Unit\Lib\ChainBundle\EventSubscriber;

use Lib\BarBundle\Command\HiCommand;
use Lib\ChainBundle\EventSubscriber\ConsoleCommandSubscriber;
use Lib\ChainBundle\Manager\ChainCommandManager;
use Lib\FooBundle\Command\HelloCommand;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ConsoleCommandSubscriberTest.
 *
 * @covers \Lib\ChainBundle\EventSubscriber\ConsoleCommandSubscriber
 */
final class ConsoleCommandSubscriberTest extends TestCase
{
    /**
     * @var ConsoleCommandSubscriber
     */
    private ConsoleCommandSubscriber $consoleCommandSubscriber;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $chainCommandLogger;

    /**
     * @var ChainCommandManager
     */
    private ChainCommandManager $chainCommandManager;

    /**
     * @var Application
     */
    private Application $application;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->chainCommandLogger = $this->createStub(LoggerInterface::class);
        $this->chainCommandManager =$this->createStub(ChainCommandManager::class);
        $this->consoleCommandSubscriber = new ConsoleCommandSubscriber($this->chainCommandLogger, $this->chainCommandManager);

        $this->application = new Application();

        $command = new HelloCommand();
        $command2 = new HiCommand();
        $this->application->addCommands([$command, $command2]);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->consoleCommandSubscriber);
        unset($this->chainCommandLogger);
        unset($this->chainCommandManager);
        unset($this->application);
    }


    /**
     * @return void
     */
    public function testOnCommandforParent(): void
    {
        $this->chainCommandManager
            ->expects($this->once())
            ->method('isChildCommand')->willReturn(false);

        $this->chainCommandManager
            ->expects($this->once())
            ->method('isParentCommand')->willReturn(true);

        $this->chainCommandManager
            ->expects($this->once())
            ->method('getParentCommand')->willReturn([
                [
                    "command" => "bar:hi",
                    "args" => []
                ]
            ]);

        $id = new InputDefinition([
            new InputArgument('command', 1)
        ]);
        $command = $this->application->find('foo:hello');
        $input = new ArrayInput(
            ['command' => 'foo:hello'],
        );
        $output = new BufferedOutput();
        $event = new ConsoleCommandEvent(
            $command,
            $input,
            $output
        );

        $this->consoleCommandSubscriber->onCommand($event);

        $this->assertSame(false, $event->commandShouldRun());

        $this->assertStringContainsString('Hi from Bar', $event->getOutput()->fetch());
    }

    /**
     * @return void
     */
    public function testOnCommandforChild(): void
    {
        $this->chainCommandManager
            ->expects($this->once())
            ->method('isChildCommand')->willReturn(true);

        $this->chainCommandManager
            ->expects($this->once())
            ->method('getChildCommand')->willReturn([
                "parent" => "foo:hello"
            ]);
        $id = new InputDefinition([
            new InputArgument('command', 1)
        ]);
        $command = $this->application->find('bar:hi');
        $input = new ArrayInput(
            ['command' => 'bar:hi'], $id
        );
        $output = new BufferedOutput();
        $event = new ConsoleCommandEvent(
            $command,
            $input,
            $output
        );

        $this->consoleCommandSubscriber->onCommand($event);

        $this->assertSame(false, $event->commandShouldRun());

        $this->assertStringContainsString('Error: bar:hi command is a member of foo:hello', $event->getOutput()->fetch());
    }
}
