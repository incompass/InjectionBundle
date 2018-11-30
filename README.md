[![Build Status](https://travis-ci.org/incompass/injection-bundle.svg?branch=master)](https://travis-ci.org/incompass/injection-bundle)
[![Latest Stable Version](https://poser.pugx.org/incompass/injection-bundle/v/stable.svg)](https://packagist.org/packages/incompass/injection-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/incompass/injection-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/incompass/injection-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/incompass/injection-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/incompass/injection-bundle/?branch=master)

Prerequisites
-------------

This bundle requires symfony 3.4+ or 4.0+

Installation
------------

### Install with composer

```bash
composer require incompass/injection-bundle
```

### Enable the bundle

In bundles.php add

```php
return [
    // ...
    Incompass\InjectionBundle\InjectionBundle::class => ['all' => true]
    // ...
];
```

Usage
-----

To configure environment groups, add something like to the following do your bundle configuration:

```php
$c->loadFromExtension('injection', [
    'environment_groups' => [
        [
            'group' => 'all',
            'environments' => ['dev', 'staging', 'prod']
        ],
        [
            'group' => 'prod-like',
            'environments' => ['staging', 'prod']
        ],
    ]
]);

```

To inject a simple service that does not require any special configuration do the following:

```php
/**
  * @Inject()
  */
class SomeService {
    // ...
}
```

By default, the bundle will use the class name as the service id. If you would like to change the id, use the id parameter in the `@Inject` annotation:

```php
/**
  * @Inject(id="some_service")
  */
class SomeService {
    // ...
}
```

More documentation coming soon