
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/Fabricio872/random-message-bundle)
![GitHub last commit](https://img.shields.io/github/last-commit/Fabricio872/random-message-bundle)
![Packagist Downloads](https://img.shields.io/packagist/dt/Fabricio872/random-message-bundle)
![GitHub Repo stars](https://img.shields.io/github/stars/Fabricio872/random-message-bundle?style=social)

# Random messages 

Symfony bundle that gives you various funny messages you can display on loading screen or anywhere where you need some placeholder content

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require fabricio872/random-teapot-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require fabricio872/random-message-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php
return [
    // ...
    Fabricio872\RandomMessageBundle\RandomMessageBundle::class => ['all' => true],
];
```

## Configuration options
```yaml
# config/services.yaml

# ...

# Default configuration for extension with alias: "random_message"
random_message:

  # Define default path where list of messages will be stored.
  path:                 '%kernel.project_dir%/var/random_messages'


# ...
```

# Usage

For receiving random message you can use Dependency injection inside your controller
```php
// src/Controller/SomeController.php

    // ...

    #[Route('/some_path', 'some_name')]
    public function some_name(RandomMessage $randomMessage)
    {
        $message = $randomMessage->getMessage();
        
        return $this->render('some-view.html.twig', [
            'randomMessage'=> $message
        ]);
    }
    
    // ...
```

or it can be used directly inside your template:

```twig
{# some Twig template #}

    {# ... #}

    {{ random_messge() }}

    {# ... #}
```
 