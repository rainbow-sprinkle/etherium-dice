<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Betslip;

class FreezeBetslips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freeze:betslips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Freeze all betslips that have reached their freeze time';


    /**
     * Execute the console command.
     */
    public function handle()
{
    // Get all the betslips that should be frozen
    $betslips = Betslip::where('freeze_time', '<=', now())
        ->where('status', '!=', 'frozen')
        ->get();

    // Loop through each betslip and freeze it
    foreach ($betslips as $betslip) {
        $betslip->status = 'frozen';
        $betslip->save();
    }
}
}
