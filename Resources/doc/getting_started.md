GeneratorBundle
=====================

 [Introduction](introduction.md#generatorbundle)
| **Getting started]**
| [Usage](usage.md#generatorbundle)
| [Configuration](configuration.md#generatorbundle)
| [templates](templates.md#generatorbundle)
| [Metadata](metadata.md#generatorbundle)
| [Questions](questions.md#generatorbundle)


## Getting Started

Run `composer require k3ssen/generator:dev-master --dev` in your console. 

Symfony Flex should add the bundle automatically to your `config/bundles.php`.

If installation fails due to minumum-stability, you could add the 
following settings to your composer.json file first:
    
    "minimum-stability": "dev",
    "prefer-stable": true 

### Required/Recommended bundles

No other bundles are strictly required for using this bundle, but to
take full advantage of this bundle, you may need
the following:

- [StofDoctrineExtensionsBundle](http://symfony.com/doc/master/bundles/StofDoctrineExtensionsBundle/index.html).  
The generator includes questions for using SoftDeleteable, Timestampable and
Blameable behaviours. 
If you don't have StofDoctrineExtensionsBundle enabled, these questions will
be skipped.
