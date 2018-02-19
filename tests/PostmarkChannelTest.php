<?php

namespace Spur\Postmark\Tests;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use Postmark\PostmarkClient;
use Spur\Postmark\PostmarkChannel;
use Spur\Postmark\PostmarkMessage;

class PostmarkChannelTest extends TestCase
{
    /**
     * @var PostmarkClient
     */
    protected $client;

    /**
     * @var Markdown
     */
    protected $markdown;

    /**
     * @var Notification
     */
    protected $notification;

    public function setUp()
    {
        $this->client = Mockery::mock(PostmarkClient::class);
        $this->markdown = Mockery::mock(Markdown::class);
        $this->channel = new PostmarkChannel($this->client, $this->markdown);
        $this->notification = new TestNotification;
        $this->notifiable = new TestNotifiable;
        $this->mailable = new TestMailable;
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $notification = $this->notification->toPostmark($this->notifiable);

        $subject = $notification->subject;

        $this->client->shouldReceive('send');

        $this->channel->send($this->notifiable, $this->notification);
    }

    /** @test */
    public function it_can_receive_a_mailable()
    {
        $mailable = $this->notification->toPostmark($this->notifiable);

        $subject = $mailable->subject;

        $this->client->shouldReceive('send');

        $this->channel->send($this->notifiable, $this->mailable);
    }
}

class TestNotifiable
{
    use Notifiable;
}

class TestNotification extends Notification
{
    public function toPostmark($notifiable)
    {
        return (new PostmarkMessage());
    }
}

class TestMailable extends Notification
{
    public function toPostmark($notifiable)
    {
        return (new Mailable());
    }
}
