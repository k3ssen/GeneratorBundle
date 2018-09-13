GeneratorBundle
=====================

 [Introduction](introduction.md#generatorbundle)
| **Getting started]**
| [Usage](usage.md#generatorbundle)
| [Configuration](configuration.md#generatorbundle)
| [Abstract classes](abstract_classes.md#generatorbundle)
| [templates](templates.md#generatorbundle)
| [Metadata](metadata.md#generatorbundle)
| [Questions](questions.md#generatorbundle)


## Getting Started

Run `composer require k3ssen/generator:dev-master --dev` in your console. 

If installation fails due to minumum-stability, you could add the 
following settings to your composer.json file first:
    
    "minimum-stability": "dev",
    "prefer-stable": true 


Symfony Flex should add the bundle automatically to your `config/bundles.php`.

### Without Flex

If you're not using symfony flex, make sure you add this bundle to your AppKernel:

```php
<?php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new K3ssen\GeneratorBundle\GeneratorBundle(),
        );
    }
}
```

You'll probably want to add the following configuration to you `app/config.yml`:

```yaml
generator:
    default_bundle: AppBundle
    templates_directory: '%kernel.root_dir%/resources/views'
```


### Required/Recommended bundles

No other bundles are strictly required for using this bundle, but to
take full advantage of this bundle, you may need
the following:

- [StofDoctrineExtensionsBundle](http://symfony.com/doc/master/bundles/StofDoctrineExtensionsBundle/index.html).  
The generator includes questions for using SoftDeleteable, Timestampable and
Blameable behaviours. 
If you don't have StofDoctrineExtensionsBundle enabled, these questions will
be skipped.
- [DatatablesBundle](https://github.com/stwe/DatatablesBundle)
for datatables.  
Generated Datatable classes needs this bundle to work. Without this bundle enabled
no question will be asked for using datatables. The generated index-action will
then use a plain table instead.