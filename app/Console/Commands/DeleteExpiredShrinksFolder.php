<?php

namespace App\Console\Commands;

use App\Shrink;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredShrinksFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shrink:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all expired shrinks';

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $expired;

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
     * @return void
     */
    public function handle()
    {
        if (!$this->findExpired()) {
            $this->line('no expired shrinks ' . $this->expired->count());

            return;
        }

        $this->deleteFolders();
        $this->line("{$this->expired->count()} expired shrinks found and deleted");
    }

    /**
     * @return bool
     */
    private function findExpired()
    {
        $this->expired = Shrink::whereDate('expire_at', '<=', Carbon::now())
            ->where('status', 0)
            ->get();

        return (bool)$this->expired->count();
    }

    private function deleteFolders()
    {
        $this->expired->each->deleteFolder();
    }

}
