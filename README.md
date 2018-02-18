# Postmark notification channel for Laravel

This adds a Postmark notification channel for Laravel that allows your app to leverage the Postmark API for tracking events related to your emails, like opens, bounces, clicks, etc.

## Contents

- [Installation](#installation)
    - [Setting up the Postmark service](#setting-up-the-Postmark-service)
- [Usage](#usage)
- [Security](#security)
- [Credits](#credits)
- [License](#license)


## Installation

```bash
$ composer require appletonlearning/laravel-postmark-channel
```

If using Laravel 5.5+ the package will be auto-discovered.

### Setting up the Postmark service

1. Get the API key for your server on Postmark
2. Add the key to your environment config as `POSTMARK_KEY`

## Usage

In every `Notifiable` model you wish to be notifiable via Postmark, you must add an email address to that model accessible through a `routeNotificationForPostmark` method:
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
In fact, the Postmark channel has the same API as the built-in Laravel `mail` channel. Follow the Laravel [Mail Notifications](https://laravel.com/docs/master/notifications#mail-notifications) and [Markdown Mails Notifications](https://laravel.com/docs/master/notifications#markdown-mail-notifications) documentation for full options.

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
