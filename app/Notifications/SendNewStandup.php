<?php

namespace App\Notifications;

use App\Standup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use App\SlackSetting;

class SendNewStandup extends Notification
{
    use Queueable;
    private $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $standup;
    public $yesterday;
    public function __construct(Standup $standup, $yesterday )
    {
        $this->standup = $standup;
        $this->yesterday = $yesterday;
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
    public function toSlack($notifiable)
    {

        $slack = SlackSetting::first();
        return (new SlackMessage())
            ->from(config('app.name'))
            ->image($slack->slack_logo_url)
            ->content('*' . ucwords($notifiable->name) . '* start')
            ->attachment(function ($attachment) {
                $attachment->title('Yesterday:')
                    ->content($this->yesterday);
            })
            ->attachment(function ($attachment) {
                $attachment->title('Today:')
                    ->content($this->standup->todays_Work);
            });;
    }
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
