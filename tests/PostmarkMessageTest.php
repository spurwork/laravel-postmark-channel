<?php

namespace Spur\Postmark\Tests;

use Spur\Postmark\PostmarkMessage;

class PostmarkMessageTest extends TestCase
{
    /** @var GcmMessage */
    protected $message;

    public function setUp()
    {
        parent::setUp();

        $this->message = new PostmarkMessage();
    }

    /** @test */
    public function it_can_set_the_greeting()
    {
        $this->message->greeting('Hello');
        $this->assertEquals('Hello', $this->message->greeting);
    }

    /** @test */
    public function it_can_set_the_subject()
    {
        $this->message->subject('My Subject');
        $this->assertEquals('My Subject', $this->message->subject);
    }

    /** @test */
    public function it_can_set_intro_lines()
    {
        $this->message->line('Intro');
        $this->assertContains('Intro', $this->message->introLines);
    }

    /** @test */
    public function it_can_set_the_view()
    {
        $this->message->view('emails.name', ['id' => 1234]);
        $this->assertEquals('emails.name', $this->message->view);
        $this->assertEquals(['id' => 1234], $this->message->viewData);
    }

    /** @test */
    public function it_can_set_the_markdown()
    {
        $this->message->markdown('emails.name', ['id' => 1234]);
        $this->assertEquals('emails.name', $this->message->markdown);
        $this->assertEquals(['id' => 1234], $this->message->viewData);
    }
}
