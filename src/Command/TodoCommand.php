<?php

namespace App\Command;

use App\Service\TodoService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TodoCommand extends Command
{
    protected static $defaultName = 'app:todo';

    /**
     * @var TodoService
     */
    private $todoService;

    /**
     * @param TodoService $todoService
     */
    public function __construct(TodoService $todoService)
    {
        parent::__construct();

        $this->todoService = $todoService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Show all todo lists');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lists = $this->todoService->getLists();

        $table = new Table($output);
        $table->setHeaders(['id', 'name', 'total']);
        foreach ($lists as $list) {
            $table->addRow([$list['id'], $list['name'], $list['total_tasks']]);
        }
        $table->render();
    }
}
