<?php

namespace App\Listeners;

use App\Events\CommentSubmitted;
use App\Mail\CommentSubmittedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Mails the post author + the moderation inbox when a new comment is submitted.
 *
 * Queued (ShouldQueue) — the email is fire-and-forget and must never block or
 * roll back the comment write. See REFACTOR_PLAN.md §1c.
 */
class SendCommentNotification implements ShouldQueue
{
    public function handle(CommentSubmitted $event): void
    {
        $recipients = $this->recipientsFor($event->comment);

        // Send one message per recipient so the post author and the moderation
        // inbox never see each other's address.
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new CommentSubmittedMail($event->comment));
        }
    }

    /**
     * The post author plus the admin-configured moderation inbox, de-duplicated
     * and excluding the commenter's own address (no self-notification).
     *
     * @return array<int, string>
     */
    private function recipientsFor($comment): array
    {
        $emails = [];

        $author = optional($comment->post)->author;
        if ($author && ! empty($author->email)) {
            $emails[] = $author->email;
        }

        $moderation = get_contact_email() ?: config('mail.contact_address');
        if (! empty($moderation)) {
            $emails[] = $moderation;
        }

        $self = optional($comment->user)->email;

        return array_values(array_unique(array_filter(
            $emails,
            fn ($email) => $email !== $self
        )));
    }
}
