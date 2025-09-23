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

// #[AsCommand(name: 'make:etapa')]
class CreateEtapa extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'make:etapa';
    protected static $name = 'make:etapa';

    protected function configure(): void
    {
        $this->addArgument(
            'processo',
            InputArgument::OPTIONAL,
            'Nome do processo'
        );

        $this->addArgument(
            'etapa',
            InputArgument::OPTIONAL,
            'Nome da etapa'
        );

        $this->addOption(
            'context',
            null,
            InputOption::VALUE_REQUIRED,
            'Qual o contexto da etapa (Comercial, Configuração ou Geral)'
        );
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');

        $processo = $input->getArgument('processo');
        if (!$processo) {
            $question = new ConfirmationQuestion('Informe a processo: ', 'cadastro');
            $processo = $questionHelper->ask($input, $output, $question);
            $input->setArgument('processo', $processo);
        }

        $etapa = $input->getArgument('etapa');
        if (!$etapa) {
            $question = new ConfirmationQuestion('Informe a etapa: ', 'cadastro');
            $etapa = $questionHelper->ask($input, $output, $question);
            var_dump($input->getArguments());
            $input->setArgument('etapa', $etapa);
        }

        $context = $input->getOption('context');
        if (!$context) {
            $question = new ConfirmationQuestion('Informe o contexto: ', 'Comercial');
            $context = $questionHelper->ask($input, $output, $question);
            $input->setOption('context', $context);
        }
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        var_dump($input->getArguments());
        $params = (object)$input->getArguments();

        $entrada = $this->createXml($params->etapa, $params->processo);
        echo $entrada;
        // $processo->asXML('./teste.xml');
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

    public function createXml($step, $preocess)
    {
        $processo = new SimpleXMLElement('<processo/>', '1');

        $processo->addAttribute('codigo', $step);
        $processo->addAttribute('modulo', ucfirst($preocess));
        $processo->addAttribute('permissao', '');
        $processo->addAttribute('entidadeRetorno', '');

        $entrada = $processo->addChild('entrada');
        $campo = $entrada->addChild('campo');
        $campo->addAttribute('regra', 'Opcional');
        $campo->addAttribute('chave', ucfirst($preocess) . "::id");
        $campo->addAttribute('nome', 'id');

        $atividades = $processo->addChild('atividades');
        $atividade = $atividades->addChild('atividade');
        $atividade->addAttribute('codigo', $step);
        $atividade->addAttribute('tipo', 'tarefa');

        $entidade = $atividade->addChild('entidade');
        $entidade->addAttribute('nome', ucfirst($step));
        $entidade->addAttribute('metodo', $step);

        $processo->addChild('saida');

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($processo->asXML());
        return $dom->saveXML();
    }
}