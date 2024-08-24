<?php

namespace Jonatas\Cli;

use DOMDocument;
use SimpleXMLElement;
// use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class ExampleCommand extends Command
{
    protected static $defaultName = 'example:show';

    protected function configure(): void
    {
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'Type your name'
        );

        $this->addOption(
            'redpandafan',
            'red',
            InputOption::VALUE_REQUIRED,
            'You`re a red-panda fan?'
        );
    }

    protected function interact(InputInterface &$input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');

        $name = $input->getArgument('name');
        if (!$name) {
            $question = new ConfirmationQuestion('Type your name: ');
            $name = $questionHelper->ask($input, $output, $question);
            $input->setArgument('name', $name);
        }

        $redPanda = $input->getOption('redpandafan');
        if (!$redPanda) {
            $question = new ConfirmationQuestion('You`re a red-panda fan? (Y or n)', 'Y');
            $redPanda = $questionHelper->ask($input, $output, $question);
            $input->setOption('redpandafan', $redPanda);
        }
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $params = (object) $input->getArguments();

        $outputText = "That's the $params->name, ";

        if ($params->redpandafan == 'Y') {
            $outputText .= 'a huge red-panda fan. :)';
        } else {
            $outputText .= 'is paia :(';
        }

        echo $outputText;
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}
