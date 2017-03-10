<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * RunActionCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class RunActionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:run-action')
            ->setDescription('Run an action on an entity or list of entities')
            ->addArgument('action', InputArgument::REQUIRED)
            ->addArgument('entity', InputArgument::REQUIRED, 'An entity id or list of entity ids, separated with commas')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->getContainer()->get('perform_base.action_runner')
                  ->run($input->getArgument('action'), $input->getArgument('entity'), []);

        $output->writeln($response->getMessage());
    }
}