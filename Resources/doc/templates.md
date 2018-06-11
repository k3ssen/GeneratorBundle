GeneratorBundle
===============

## Templates

Every generated file is rendered through twig. This enables you to override any
twig file to decide what the final output should look like.

Using `php bin/console generator:templates` in your console, files that extend
these templates will be generated in your `templates/bundle/GeneratorBundle`
directory. This should give you a quick start for making alterations without
having to search each file independently.


### Example for overriding template-file 
Generated php-files will all start with the following lines:  
```
<?php
//TODO: THIS FILE IS AUTO-GENERATED - REMOVE THIS COMMENT TO MAKE CLEAR THAT YOU'VE REVIEWED THIS FILE
declare(strict_types=1);
```

Let's say you want to get rid of the line with 'TODO', because you find this annoying.

After running `php bin/console generator:templates` in your console, you can navigate to
`templates/bundles/GeneratorBundle/Skeleton/_strict_types_declaration.php.twig`.
In this file you'll find the following content:
```
{# @var meta_entity \K3ssen\GeneratorBundle\MetaData\MetaEntityInterface #}
{# @var generate_options \K3ssen\GeneratorBundle\Generator\CrudGenerateOptions#}
{% use '@!Generator/skeleton/_strict_types_declaration.php.twig' %}
```

If you navigate to the reference after `{% use`, you should find the original file with the following content:
```
{% block strict_types_declaration %}
//TODO: THIS FILE IS AUTO-GENERATED - REMOVE THIS COMMENT TO MAKE CLEAR THAT YOU'VE REVIEWED THIS FILE
declare(strict_types=1);
{% endblock %}
```

You can copy this content into your `templates/bundles/GeneratorBundle/Skeleton/_strict_types_declaration.php.twig`
file and have the line with 'TODO' removed.

Similarly you can change any other file. 


**Note:** The above example is a bit of an exception, since it's the only file
that is being used across different files.


### File name conventions

#### Files without underscore

Files that don't start with an underscore, such as `Entity.php.twig`, are directly loaded by the generator, so
if you want to make any changes these files usually should be your starting point.


#### Files starting with underscore

All files starting with an underscore `_` are only used in other twig files and will never directly be loaded by
the generator. 

E.g. the file `_usages.php.twig` in the `entity` directory is used withing the `Entity.php.twig` file. 
If you override the entity file, then you choose not to use `_usages.php.twig` at all and remove that file.


**Note:**
You might notice that `properties.php.twig` has no underscore, but is used within `Entity.php.twig`
just like other files that do begin with an underscore.  
This is because the properties file has the additional purpose of being used to render only properties
when adding fields to an existing entity.  
(the same applies to `property_methods.php.twig`)