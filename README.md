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

TBD

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