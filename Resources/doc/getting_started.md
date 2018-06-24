GeneratorBundle
=====================

 [Introduction](Introduction.md#GeneratorBundle)
| **Getting started]**
| [Usage](usage.md#GeneratorBundle)
| [Configuration](configration.md#GeneratorBundle)
| [templates](templates.md#GeneratorBundle)
| [Metadata](metadata.md#GeneratorBundle)
| [Questions](questions.md#GeneratorBundle)


## Getting Started

Run `composer require k3ssen/generator:dev-master --dev` in your console. 


Symfony Flex should add the bundle automatically to your `config/bundles.php`.

### Required/Recommended bundles

No other bundles are strictly required for using this bundle, but to
take full advantage of this bundle, you may need
the following:

- [StofDoctrineExtensionsBundle](http://symfony.com/doc/master/bundles/StofDoctrineExtensionsBundle/index.html).  
The generator includes questions for using SoftDeleteable, Timestampable and
Blameable behaviours. 
If you don't have StofDoctrineExtensionsBundle enabled, these questions will
be skipped.
