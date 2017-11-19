<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Symfony\Component\Console\Input\InputOption;

class ESReindexCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'es:reindex {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run ES reindexing for the given type.';

    /**
     * Mapping of index types and which re-indexing class to instantiate
     *
     * @var array
     */
    protected $indexTypes = [];

    public function __construct(array $indexTypes = [])
    {
        $this->indexTypes = $indexTypes;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $indexType = $this->input->getOption('type');

        if (!in_array($indexType, array_keys($this->indexTypes))) {
            $this->error('Index type specified is unknown');
            return;
        }

        $reIndexService = App::make($this->indexTypes[$indexType]);

        $reIndexService->reindex();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['type', null, InputOption::VALUE_REQUIRED, 'The type to re-index.'],
        ];
    }

}
