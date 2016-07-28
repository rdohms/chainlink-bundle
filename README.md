# DMS Chainlink Symfony Bundle

[![Latest Version](https://img.shields.io/github/release/rdohms/chainlink-bundle.svg?style=flat-square)](https://github.com/rdohms/chainlink-bundle/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/rdohms/chainlink-bundle/master.svg?style=flat-square)](https://travis-ci.org/rdohms/chainlink-bundle)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/rdohms/chainlink-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/rdohms/chainlink-bundle/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/rdohms/chainlink-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/rdohms/chainlink-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/rdohms/chainlink-bundle.svg?style=flat-square)](https://packagist.org/packages/rdohms/chainlink-bundle)

This Bundle wraps Chainlink library and offers a drop in solution to implement the Chain of Responsibility pattern, based on Symfony service tags. It allows you to, via configuration, setup multiple contexts and define which tags provide handlers for each.

## Installation

To get the Bundle code, run:

```sh
composer require dms/chainlink-bundle
```

Edit your `AppKernel.php` file to instantiate the Bundle:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new DMS\Chainlink\Bundle\DMSChainlinkBundle(),
    );
}
```

If you are looking for other frameworks, check [Packagist](http://packagist.org/packages/dms/) for wrappers and adapters.

## Usage

To register new Contexts and assign handlers to them, simply add configuration entries in your `config.yml`.

```yml

dms_chainlink:
    contexts:
        my_new_context:
            tag: mycontext.handler
```

The bundle will look for any services tagged with the `tag` defined above and inject them as handlers in the context you requested.

To handle a request, retrieve the Context from the Container and pass in your input.

```php
$this->container->get('dms_chainlink.context.my_new_context')->handle($input);

//or its also aliased at

$this->container->get('my_new_context')->handle($input);
```

Services that will be used as handlers need to implement the `HandlerInterface` from Chainlink. Its the handler's responsibility to identify which input it is responsible for, the interface contains a `handles` method that is called for that.

## Order of Chain handling

As of version 0.3, Chainlink supports ordering of the handlers using the priority system used extensively in Symfony. The handlers will be called from high to low. 

```yml

# src/Vendor/MyBundle/Resources/config/services.yml

my_service:
  class: MyHandler
  tag:
    - { name: my_new_context, priority: 1 }

my_other_service:
  class: OtherHandler
  tag:
    - { name: my_new_context, priority: 9001 }
```

In this case `OtherHandler` will be called first, and then `MyHandler`, provided they can handle the usecase.
