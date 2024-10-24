<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Post;

class PostPublished extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Post Published: ' . $this->post->title)
            ->line('A new post has been published.')
            ->action('View Post', url('/posts/' . $this->post->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
