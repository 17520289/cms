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

use function PHPSTORM_META\type;

class StandupDaily extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct()
    { }

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
        $slack  = explode(',' , SlackSetting::first()->slack_webhook);
        $SLACK_WEBHOOK = $slack[1];
        $messLeave = 'Chào ngày mới :sunrise: chúc các bạn team Nhân sự ngày mới làm việc vui vẻ :heart:'.PHP_EOL.PHP_EOL;
        $messLeaveDaily = '';
        $messBirthDay = '';
        $leavesToday =  Leave::where('status', 'approved')->where('leave_date', Carbon::now()->format('Y-m-d'))
            ->get();
        $userBirthDay =  EmployeeDetails::where('date_of_birth', Carbon::now()->format('Y-m-d'))->get();
        foreach ($leavesToday as $leave) {
            $leave->name = $leave->user->name;
        }
        if($leavesToday->count()!=0){
            $messLeaveDaily .= '*Hôm nay đồng chí này off nè mọi người :smiling_face_with_tear::* '.PHP_EOL;
            $messLeave .= '--------------------------------------------------------------'.PHP_EOL.PHP_EOL.
            '*Hôm nay đồng chí này off nè mọi người :smiling_face_with_tear:*:'.PHP_EOL;
            foreach ($leavesToday as $leave) {
                $halfDay = '';
                if($leave->duration == 'half day'){
                    $halfDay = $leave->mor_or_aft == 'morning' ? '   |   Nghỉ buổi sáng.' : '   |   Nghỉ buổi chiều.';
                }
                $messLeaveDaily .= '* - Tên:* ' . $leave->user->name . $halfDay .PHP_EOL;
                $messLeave = $messLeave . '* - Tên:* ' . $leave->user->name .  $halfDay .'   |   *Lý do:* ' . $leave->reason . PHP_EOL;
                
            }
            $messLeave .= 'Đăng nhập vào hệ thống để xem chi tiết  <http://is.hlsolutions.jp/admin/leave/all-leaves | is.hlsolutions.jp >  :star-struck: :smile:';
        }
        if($userBirthDay->count() !=0){
            $messBirthDay .= "*Chúc mừng sinh nhật:* ".PHP_EOL;
            foreach ($userBirthDay as $birthDay) {
                $messBirthDay = $messBirthDay . 'Chúc mừng sinh nhật bạn *' . $birthDay->user->name . '*. Chúc bạn luôn vui vẻ, thuận buồm xuôi gió trong công việc và hạnh phúc trong tình duyên. *HPBD*:birthday: ' . PHP_EOL;
            }
        }
        
       
        //send message to channel hls_hr 
        $data = 'payload=' . json_encode(array(
            'channel'  => '#test',
            'text'     => $messLeave,
            "icon_emoji"    => ':heart:'
        ));

        
        $c = curl_init($SLACK_WEBHOOK);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        curl_exec($c);
        curl_close($c);
        
        //send mesasge daily
        $messDaily = '*Các bạn ơi đừng ngủ nữa, dậy đi nào, dậy start kiếm cơm nào!*:heart:'.PHP_EOL
                    .'------------------------------------------------------------------------'.
                    PHP_EOL.$messLeaveDaily.$messBirthDay;
        return (new SlackMessage())
            ->from(config('app.name'))
            ->content($messDaily);
    }
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
