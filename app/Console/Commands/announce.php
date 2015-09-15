<?php

namespace VacStatus\Console\Commands;

use Illuminate\Console\Command;

use VacStatus\Models\Announcement;

class announce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announce {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an announcement.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $message = $this->argument('message');

        $announcement = new Announcement(['value' => $message]);
        if(!$announcement->save())
        {
            $this->error('Something broke trying to update the Announcement.');
            return;
        }

        $this->info('Announcement was successfully updated.');
    }
}
