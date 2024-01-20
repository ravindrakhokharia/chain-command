<?php

namespace Lib\ChainBundle\EventSubscriber;

use Lib\ChainBundle\Manager\ChainCommandManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;
class ConsoleCommandSubscriber implements EventSubscriberInterface
{
    private ?Application $application = null;
    private ?OutputInterface $globalOutput = null;
    public function __construct(
        private readonly LoggerInterface $chainCommandLogger,
        private readonly ChainCommandManager $chainCommandManager,
    ) {
    }

    public function onCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        $commandName = $command->getName();

        $this->globalOutput = $event->getOutput();
        $this->application = $command->getApplication();

        if ($this->chainCommandManager->isChildCommand($commandName)) {
            $event->disableCommand();
            $childCommand = $this->chainCommandManager->getChildCommand($commandName);
            $this->globalOutput->writeln(sprintf('Error: %s command is a member of %s command chain and cannot be executed on its own.', $commandName, $childCommand['parent'] ));
        }

        if ($this->chainCommandManager->isParentCommand($commandName)) {
            $event->disableCommand();
            $this->handleParentCommand($event->getCommand());
        }

        $this->application = null;
        $this->globalOutput = null;
    }

    public function onTerminate(ConsoleTerminateEvent $event): void
    {
        $command = $event->getCommand();
        $commandName = $command->getName();
        if ($this->chainCommandManager->isParentCommand($commandName)) {
            $event->setExitCode(Command::SUCCESS);
        }
    }

    private function handleParentCommand(Command $command)
    {
        $commandName = $command->getName();
        $parentCommand = $this->chainCommandManager->getParentCommand($command->getName());
        // Pre Log
        $this->log(sprintf("%s is a master command of a command chain that has registered member commands", $commandName));
        foreach ($parentCommand as $child) {
            $this->log(sprintf("%s registered as a member of %s command chain", $child['command'], $commandName));
        }
        $this->log(sprintf('Executing %s command itself first:', $commandName));

        // Execute Parent Command
        $input = new ArrayInput(['command' => $commandName], $command->getDefinition());
        $this->executeCommand($commandName, $input);

        foreach ($parentCommand as $child) {
            $this->log(sprintf('Executing %s chain members:', $child['command']));

            // Execute Child Command
            $input = new ArrayInput(array_merge(['command' => $child['command']], $child['args']));
            $this->executeCommand($child['command'], $input);
        }

        // Post Log
        $this->log(sprintf('Execution of %s chain completed.', $commandName));
    }

    private function log(string $message): void
    {
        $this->chainCommandLogger->log("info", $message);
    }

    private function executeCommand(string $commandName, ArrayInput $input): BufferedOutput
    {
        $localoutput = new BufferedOutput();

        $this->application->find($commandName)->run($input, $localoutput);

        $outputString = $localoutput->fetch();
        $this->globalOutput->write($outputString);
        $this->log($outputString);

        return $localoutput;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onCommand',
            ConsoleEvents::TERMINATE => 'onTerminate',
        ];
    }

}
