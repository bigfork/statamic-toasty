# Aardvark SEO Documentation

## Installation

#### Install via composer:
```
composer require withcandour/statamic-toasty
```
Then publish the publishables from the service provider:
```
php artisan vendor:publish --provider="WithCandour\StatamicToasty\ToastyServiceProvider"
```

### Enabling
By default this addon will look at the `TOASTY_ENABLED` environment variable (true/false) to determine whether the warmer should be enabled.

### Config
After installing, a config file will be created at `config/statamic/toasty.php`. This will give you control over a number of config options:

| Setting   | Type       | Description                                                 |
| --------- | ---------- | ----------------------------------------------------------- |
| `enabled` | Boolean    | Whether toasty is enabled or not.                           |
| `sites`   | Array      | An array of site handles to warm                            |
| `crawlable_query_parameters`   | Array      | Query parameters for which pages should be crawled |
