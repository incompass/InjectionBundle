[![Build Status](https://travis-ci.com/incompass/injection-bundle.svg?branch=master)](https://travis-ci.org/incompass/injection-bundle)
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

By default, the bundle looks in `src` for services to inject with a base namespace of `App`. To change that, add a configuration like the following:

```php
$c->loadFromExtension('injection', [
    'paths' => [
        'code' => 'MyApp\\'
    ]
]);
```

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

### Basic Injection

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

### Service ids

By default, the bundle will use the class name as the service id. If you would like to change the id, use the id parameter in the `@Inject` annotation:

```php
/**
  * @Inject(id="some_service")
  */
class SomeService {
    // ...
}
```

### Arguments

To add an argument to a service, add an argument property with `@Argument` annotations. Multiple arguments can be added.

```php
/**
  * @Inject(
  *     arguments={
  *         @Argument(name="constructorParameterName", value="%parameter_name%")
  *     }
  * )
  */
class SomeService {
    // ...
}
```

To add a reference to another service as an argument value, preface the value with `@`:

```php
/**
  * @Inject(
  *     arguments={
  *         @Argument(name="constructorParameterName", value="@SomeOtherServiceClass")
  *     }
  * )
  */
class SomeService {
    // ...
}
```

### Tags

To tag a service, add a tag property with `@Tag` annotations. Multiple tags can be added.

```php
/**
  * @Inject(
  *     tags={
  *         @Tag(name="doctrine.orm.entity_listener", attributes={
  *             "entity"=User::class,
  *             "event"=\Doctrine\ORM\Events::prePersist,
  *             "method"="prePersist"
  *         })
  * )
  */
class SomeService {
    // ...
}
```

Other Features not documented
-----------------------------

* Factories
* Method Calls
* Environments
* Child Definitions
* Aliases
* Abstract Definitions
* Auto Configuration
* Auto Wiring
* Lazy Loading
* Public/Private
* Shared

More documentation coming soon