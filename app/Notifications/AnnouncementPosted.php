<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AnnouncementPosted extends Notification
{
    use Queueable;

    public function __construct(public Announcement $announcement)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $org = $this->announcement->organization;

        return (new MailMessage)
            ->subject('New announcement: '.$this->announcement->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line(($org?->name ?? 'Your organization').' has posted a new announcement.')
            ->line('**'.$this->announcement->title.'**')
            ->line($this->announcement->body)
            ->when($org, fn ($mail) => $mail->action('View Organization Page', url('/org/'.$org->slug)))
            ->line('Thank you for being part of our community.');
    }
}
