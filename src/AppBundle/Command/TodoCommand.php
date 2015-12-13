<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TodoCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:todo')
            ->setDescription('Show all todo lists');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $todoService = $this->getContainer()->get('app.service.todo');
        $lists = $todoService->getLists();

        $table = new Table($output);
        $table->setHeaders(['id', 'name', 'total']);
        foreach ($lists as $list) {
            $table->addRow([$list['id'], $list['name'], $list['total_tasks']]);
        }
        $table->render();
    }
}
