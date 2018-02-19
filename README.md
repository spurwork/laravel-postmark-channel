[![Latest Version on Packagist](https://img.shields.io/packagist/v/appletonlearning/laravel-postmark-channel.svg?style=flat-square)](https://packagist.org/packages/appletonlearning/laravel-postmark-channel)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/appletonlearning/laravel-postmark-channel.svg?branch=master)](https://travis-ci.org/appletonlearning/laravel-postmark-channel)

# Postmark notification channel for Laravel

This adds a Postmark notification channel for Laravel that allows your app to leverage the Postmark API for tracking events related to your emails, like opens, bounces, clicks, etc.

## Contents

- [Installation](#installation)
    - [Setting up the Postmark service](#setting-up-the-Postmark-service)
- [Usage](#usage)
    - [Send an email](#send-an-email)
    - [Send a Markdown email](#send-a-markdown-email)
    - [Tracking notifications](#tracking-notifications)
- [Security](#security)
- [Credits](#credits)
- [License](#license)


## Installation

Requires PHP 7.0+


```bash
$ composer require appletonlearning/laravel-postmark-channel
```

If using Laravel 5.5+ the package will be auto-discovered.

### Setting up the Postmark service

1. Get the API key for your server on Postmark
2. Add the key to your environment config as `POSTMARK_KEY`

## Usage

### Send an email

In every `Notifiable` model you wish to be notifiable via Postmark you must add an email address to that model accessible through a `routeNotificationForPostmark` method:
```php
class User extends Eloquent
{
    use Notifiable;

    public function routeNotificationForPostmark()
    {
        return $this->email;
    }
}
```
You may now tell Laravel to send notifications using Postmark in the `via` method:
```php
use Spur\Postmark\PostmarkChannel;
use Spur\Postmark\PostmarkMessage;

class InvoiceNotification extends Notification
{
    public function via($notifiable)
    {
        return [PostmarkChannel::class];
    }

    public function toPostmark($notifiable)
    {
    	$url = url('/invoice/'.$this->invoice->id);
    
        return (new PostmarkMessage)
            ->greeting('Hello!')
            ->line('One of your invoices has been paid!')
            ->action('View Invoice', $url)
            ->line('Thank you for using our application!');
    }
}
```

### Send a Markdown email

Just like regular `mail` type notifications, you can also send Markdown emails:
```php
public function toPostmark($notifiable)
{
    $url = url('/invoice/'.$this->invoice->id);

    return (new PostmarkMessage)
        ->subject('Invoice Paid')
        ->markdown('mail.invoice.paid', ['url' => $url]);
}
```
In fact, the Postmark channel has the same API as the built-in Laravel `mail` channel. Follow the Laravel [Mail Notifications](https://laravel.com/docs/master/notifications#mail-notifications) and [Markdown Mail Notifications](https://laravel.com/docs/master/notifications#markdown-mail-notifications) documentation for full options.

### Tracking notifications

One of the benefits of using the Postmark channel over the default `mail` channel is that allows for event tracking, such as deliveries, clicks, opens, and bounces on sent emails. To track email events you must first store each notification in your database, which must at least have a column to track the email's Postmark-specific `MessageID`.

To capture the ID on send we listen to Laravel's `Illuminate\Notifications\Events\NotificationSent` event. Register a listener for this event in your `EventServiceProvider`:

```php
/**
 * The event listener mappings for the application.
 *
 * @var array
 */
protected $listen = [
    'Illuminate\Notifications\Events\NotificationSent' => [
        'App\Listeners\LogNotification',
    ],
];
```

As the [Laravel documentation](https://laravel.com/docs/master/notifications#notification-events) states:
> Within an event listener, you may access the notifiable, notification, and channel properties on the event to learn more about the notification recipient or the notification itself...

```php
/**
 * Handle the event.
 *
 * @param  NotificationSent  $event
 * @return void
 */
public function handle(NotificationSent $event)
{
    // $event->channel
    // $event->notifiable
    // $event->notification
    // $event->response
}
```
With Postmark, the `$event->response` object contains the ID, status, and other [data sent back from Postmark](https://postmarkapp.com/developer/api/email-api) when the email has been sent through their system:
```json
{
    "To": "receiver@example.com",
    "SubmittedAt": "2014-02-17T07:25:01.4178645-05:00",
    "MessageID": "0a129aee-e1cd-480d-b08d-4f48548ff48d",
    "ErrorCode": 0,
    "Message": "OK"
}
```

Once your emails are being successfully stored with Postmark's `MessageID` it becomes very easy to track all events that occur with each email using the [Postmark Webhooks API](https://postmarkapp.com/developer/webhooks/webhooks-overview).

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email adam@spurjobs.com instead of using the issue tracker.

## Credits

- [Adam Campbell](https://github.com/hotmeteor)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
