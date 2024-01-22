<?php

namespace Lib\FooBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class HelloCommand
 * 
 * Hello Command for bundle Foo, prints a static message
 */
class HelloCommand extends Command
{
    public function __construct()
    {
        parent::__construct('foo:hello');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * 
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Hello from Foo!');

        return Command::SUCCESS;
    }

    /**
     * @return string|null
     */
    public static function getDefaultDescription(): ?string
    {
        return "This is Hello command from Foo Bundle.";
    }
}
