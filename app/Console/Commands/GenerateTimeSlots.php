<?php

namespace App\Console\Commands;

use App\Models\DailyBookingsSlots;
use App\Models\TimeSlot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateTimeSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:timeslots {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $date=$this->argument('date');

        $timeslots=TimeSlot::select(DB::raw('distinct(start_time)'))->get();

        foreach($timeslots as $ts){
            if(rand(0,1)){
                $slot=TimeSlot::where('start_time', $ts->start_time)->first();
                TimeSlot::create(array_merge($slot->only('clinic_id', 'start_time','duration','internal_start_time','grade_1', 'grade_2', 'grade_3', 'grade_4', 'isactive'),['date'=>$date]));
            }


        }
        //die;
        $timeslots=DailyBookingsSlots::select(DB::raw('distinct(start_time)'))->get();

        foreach($timeslots as $ts){
            if(rand(0,1)) {
                $slot = DailyBookingsSlots::where('start_time', $ts->start_time)->first();
                DailyBookingsSlots::create(array_merge($slot->only('start_time', 'duration', 'isactive', 'internal_start_time'), ['date' => $date]));
            }

        }

    }
}
