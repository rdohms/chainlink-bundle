# Symbid Chainlink Symfony Bundle

This Bundle wraps Chainlink library and offers a drop in solution to implement the Chain of Responsibility pattern, based on Symfony service tags. It allows you to, via configuration, setup multiple contexts and define which tags provide handlers for each.

## Installation

To get the Bundle code, run:

```sh
composer require symbid/chainlink-bundle
```

Edit your `AppKernel.php` file to instantiate the Bundle:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new Symbid\Chainlink\Bundle\SymbidChainlinkBundle(),
    );
}
```

If you are looking for other frameworks, check [Packagist](http://pacakgist.org/vendor/symbid) for wrappers and adapters.

## Usage

To register new Contexts and assign handlers to them, simply add configuration entries in your `config.yml`.

```yml

symbid_chainlink:
    contexts:
        my_new_context:
            tag: mycontext.handler
```

The bundle will look for any services tagged with the `tag` defined above and inject them as handlers in the context you requested.

To handle a request, retrieve the Context from the Container and pass in your input.

```php
$this->container->get('my_new_context')->handle($input);
```

Services that will be used as handlers need to implement the `HandlerInterface` from Chainlink. Its the handler's responsibility to identify which input it is responsible for, the interface contains a `handles` method that is called for that.