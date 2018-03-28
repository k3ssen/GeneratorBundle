Installation
============

Add this bundle to 'require-dev' and the repositories in your composer.json file:

    ...
    
    "require-dev": {
    
        ...
        
        "k3ssen/generator": "dev-master"
    },
    
    ...
    
    "repositories": [
        
        {
            "type": "vcs",
            "url": "https://github.com/k3ssen/GeneratorBundle.git"
        }
    ]

Afterwars, run `composer install` to have this bundle installed. Thanks to symfony flex, this
bundle should be automatically added to your `config/bundles.php` file.

> TODO: add recipe to make sure only 'dev' environment is used for this bundle in bundesl.php

