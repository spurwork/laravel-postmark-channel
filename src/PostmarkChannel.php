<?php

namespace Spur\Postmark;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Postmark\Models\PostmarkAttachment;
use Postmark\PostmarkClient;
use Str;

class PostmarkChannel
{
    private $mailer;
    private $markdown;

    private $from;
    private $reply_to;
    private $to;
    private $cc;
    private $bcc;
    private $subject;
    private $attachments = [];
    private $priority;

    public function __construct(PostmarkClient $mailer, Markdown $markdown)
    {
        $this->mailer = $mailer;
        $this->markdown = $markdown;
    }

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toPostmark($notifiable);

        if (!$notifiable->routeNotificationFor('mail')) {
            return;
        }

        list($html, $plain) = $this->parseView($message);

        $this->buildMessage($notifiable, $notification, $message);

        return $this->mailer->sendEmail(
            $this->from,
            $this->to,
            $this->subject,
            $this->renderView($html, $message->viewData),
            $this->renderView($plain, $message->viewData),
            null,
            true,
            $this->reply_to,
            $this->cc,
            $this->bcc,
            null,
            $this->attachments,
            null
        );
    }

    protected function renderView($view, $data)
    {
        return $view instanceof Htmlable
            ? $view->toHtml()
            : $this->views->make($view, $data)->render();
    }

    protected function parseView($message)
    {
        $view = $this->buildView($message);

        if (is_string($view)) {
            return [$view, null];
        }

        if (is_array($view) && isset($view[0])) {
            return [$view[0], $view[1]];
        }

        if (is_array($view)) {
            return [
                $view['html'] ?? null,
                $view['text'] ?? null,
            ];
        }

        throw new InvalidArgumentException('Invalid view.');
    }

    protected function buildView($message)
    {
        if ($message->view) {
            return $message->view;
        }

        return [
            'html' => $this->markdown->render($message->markdown, $message->data()),
            'text' => $this->markdown->renderText($message->markdown, $message->data()),
        ];
    }

    protected function buildMessage($notifiable, $notification, $message)
    {
        $this->addressMessage($notifiable, $message);

        $this->subject = $message->subject ?: str_title(
            str_snake(class_basename($notification), ' ')
        );

        $this->addAttachments($message);

        if (!is_null($message->priority)) {
            $this->priority = $message->priority;
        }
    }

    protected function addressMessage($notifiable, $message)
    {
        $this->addSender($message);

        $this->to = $this->getRecipients($notifiable, $message);

        if ($message->cc) {
            $this->cc = $message->cc[0];
        }

        if ($message->bcc) {
            $this->bcc = $message->bcc[0];
        }
    }

    protected function addSender($message)
    {
        if (!empty($message->from)) {
            $this->from = $message->from[0];
        } else {
            $this->from = config('mail.from.address');
        }

        if (!empty($message->replyTo)) {
            $this->reply_to = $message->replyTo[0];
        }
    }

    protected function getRecipients($notifiable)
    {
        if (is_string($recipients = $notifiable->routeNotificationFor('mail'))) {
            $recipients = [$recipients];
        }

        return collect($recipients)->map(function ($recipient) {
            return is_string($recipient) ? $recipient : $recipient->email;
        })->implode(',');
    }

    protected function addAttachments($message)
    {
        foreach ($message->attachments as $attachment) {
            $this->attachments[] = PostmarkAttachment::fromFile(
                $attachment['file'],
                array_get($attachment, 'options.as'),
                array_get($attachment, 'options.mime')
            );
        }

        foreach ($message->rawAttachments as $attachment) {
            $this->attachments[] = PostmarkAttachment::fromRawData(
                $attachment['data'],
                array_get($attachment, 'name'),
                array_get($attachment, 'options.mime')
            );
        }
    }
}
