<?php

namespace Clue\PharComposer\Command;

use Clue\PharComposer\Phar\Packager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command
{
    /** @var Packager */
    private $packager;

    public function __construct(Packager $packager = null)
    {
        parent::__construct();

        if ($packager === null) {
            $packager = new Packager();
        }
        $this->packager = $packager;
    }

    protected function configure()
    {
        $this->setName('build')
             ->setDescription('Build phar for the given composer project')
             ->addArgument('project', InputArgument::OPTIONAL, 'Path to project directory or composer.json', '.')
             ->addArgument('target', InputArgument::OPTIONAL, 'Path to write phar output to (defaults to project name)')
             ->addOption('main', 'm', InputOption::VALUE_REQUIRED, 'Relative path to the entrypoint script (meant for inclusion, not executable by default)')
             ->addOption('executable', 'e', InputOption::VALUE_NONE, 'Make the resulting phar executable (effective only if --main option is used)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->packager->setOutput($output);
        $this->packager->coerceWritable();

        $pharer = $this->packager->getPharer($input->getArgument('project'));

        $target = $input->getArgument('target');
        $main = $input->getOption('main');

        if ($target !== null) {
            $pharer->setTarget($target);
        }

        if ($main !== null) {
            $pharer->setMain($main);
            $pharer->setExecutable($input->getOption('executable'));
        }

        $pharer->build();

        return 0;
    }
}
