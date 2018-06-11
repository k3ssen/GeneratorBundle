GeneratorBundle
=====================

## Getting Started

Add this git repository to your `composer.json` file:

    //...
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/k3ssen/GeneratorBundle.git"
        }
    ],
    
Run `composer require k3ssen/generator:dev-master --dev` in your console.

Symfony Flex should add the bundle automatically to your `config/bundles.php`.
To make sure, open this file and check if the following line 
is added to the array:

    
    K3ssen\GeneratorBundle\GeneratorBundle::class => ['dev' => true],
    
Add this line yourself if missing. If you see `['all' => true]`, then you
should change `all` into `dev`.


### Required/Recommended bundles

No other bundles are strictly required for using this bundle, but to
take full advantage of this bundle, you may need
the following bundles:

- [BaseAdminBundle](https://github.com/k3ssen/BaseAdminBundle)  
The GeneratorBundle is created alongside the BaseAdminBundle. This is to
make sure you can keep the GeneratorBundle for develop-environment only, while
the BaseAdminBundle contains code that is required in all environments.

- [DatatablesBundle](https://github.com/stwe/DatatablesBundle)
for datatables.  
Using the DatatablesBundle is optional, but you won't be able to generate
datatables without this bundle.

- [StofDoctrineExtensionsBundle](http://symfony.com/doc/master/bundles/StofDoctrineExtensionsBundle/index.html).  
The generator includes questions for using SoftDeleteable, Timestampable and
Blameable behaviours. 
If you don't have StofDoctrineExtensionsBundle enabled, these questions will
be skipped.
