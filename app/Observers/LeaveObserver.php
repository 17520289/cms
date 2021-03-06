<?php

namespace App\Observers;

use App\Events\LeaveEvent;
use App\Leave;
use App\LeaveType;

class LeaveObserver
{
    /**
     * Handle the leave "saving" event.
     *
     * @param  \App\Leave  $leave
     * @return void
     */
    public function saving(Leave $leave)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $leave->company_id = company()->id;
            $leaveTypes = LeaveType::where('id', $leave->leave_type_id)->first();
             $leave->paid = $leaveTypes->paid;   
        }
    }

    public function created(Leave $leave)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (request()->duration == 'multiple' ) {
                if (session()->has('leaves_duration')) {
                    event(new LeaveEvent($leave, 'created', request()->multi_date));
                }
            }else{
                if( request()->duration == 'date_range'){
                    if (session()->has('leaves_duration')) {
                        event(new LeaveEvent($leave, 'created', request()->date_range));
                    }
                } else {
                    event(new LeaveEvent($leave, 'created'));
                }
            } 
   
        }
    }
    public function updating(Leave $leave)
    {       
        $leaveTypes = LeaveType::where('id', $leave->leave_type_id)->first();
        $leave->paid = $leaveTypes->paid;
    }   
    public function updated(Leave $leave)
    {
        if (!app()->runningInConsole()) {
            if ($leave->isDirty('status')) {
                event(new LeaveEvent($leave, 'statusUpdated'));
            } else {
                event(new LeaveEvent($leave, 'updated'));
            }
        }
    }

}
