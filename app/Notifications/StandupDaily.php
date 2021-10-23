<?php

namespace App\Notifications;

use App\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use App\SlackSetting;
use Illuminate\Support\Carbon;
use App\EmployeeDetails;

class StandupDaily extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
   
    public function __construct()
    { 

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    public function toSlack($notifiable)
    {

        $slack  = SlackSetting::first();
        $messLeave = '';
        $messBirthDay = '';
        $leavesToday =  Leave::where('status', 'approved')->where('leave_date', Carbon::now()->format('Y-m-d'))
        ->get();
        $userBirthDay =  EmployeeDetails::where('date_of_birth', Carbon::now()->format('Y-m-d'))->get();
        foreach($leavesToday as $leave){
            $leave->name = $leave->user->name;
        }
        foreach ($leavesToday as $leave) {
            $messLeave = $messLeave . '*Tên:* ' . $leave->user->name . '   .   *Lý do:* ' . $leave->reason . PHP_EOL;
        }
        foreach ($userBirthDay as $birthDay) {
            $messBirthDay = $messBirthDay . 'Chúc mừng sinh nhật bạn *'.$birthDay->user->name.'*. Chúc bạn luôn vui vẻ, thuận buồm xuôi gió trong công việc và hạnh phúc trong tình duyên. *HPBD*:birthday: ' . PHP_EOL;
        }
        return (new SlackMessage())
            ->from(config('app.name'))
            ->content('*Các bạn ơi đừng ngủ nữa, dậy đi nào, dậy start kiếm cơm nào!*:heart:')
            ->attachment(function ($leavesToday) use ($messLeave) {
                if ($messLeave != '') {
                    $leavesToday->title('Thông báo nghỉ: ',)
                        ->content($messLeave);
                }
            })
            ->attachment(function ($attachment) use ($messBirthDay) {
                if($messBirthDay !=''){
                    $attachment->title('Chúc mừng sinh nhật: ',)
                    ->content($messBirthDay);
                }
               
            });
    }
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
