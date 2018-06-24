GeneratorBundle
=====================

A Symfony 4 bundle for quickly generating/prototyping a CRUD application.
It is similar to Symfony's [MakerBundle](https://github.com/symfony/maker-bundle),
except that this bundle is built to be much more extensible.

Features:
* Generate entities using interactive commands.  
  Included:
    - Define properties with their types (string, int, array, etc) and attributes (length, nullable, unique, etc) 
    - Types also include relationship-types
        - ManyToOne, OneToMany, ManyToMany, OneToOne
        - Adds fields to mapped or inversed targetEntity
        - creates target entity if it does'nt exist yet.
    - Add validations (annotations like `@Assert\Length`)
    - Add fields to existing entities
    - Add traits
    - Read existing entities and interactively add/edit/remove properties.
* Generate CRUD based on entities, including:
    - Controller (optionally use subdirectories)
    - Template (twig) files
    - Form
    - Voter (optional)

This bundle is highly customizable:
- Enable/disable questions that you do or don't need  
- Specify defaults
- All files are generated through twig files, which you can override by
using identical files in `/templates/bundles/GeneratorBundle/...`
To make things simpler, you can use the command `generate:templates` to 
have this done automatically for you.
- For complex usage, the bundle is built in a very extensible way, allowing
you to override nearly everything independently.


## Documenation

* [Introduction](Resources/doc/introduction.md#GeneratorBundle)
* [Getting started](Resources/doc/getting_started.md#GeneratorBundle)
* [Usage](Resources/doc/usage.md#GeneratorBundle)
* [Configuration](Resources/doc/configration.md#GeneratorBundle)
* [Templates](Resources/doc/templates.md#GeneratorBundle)
* [Metadata](Resources/doc/metadata.md#GeneratorBundle)
* [Questions](Resources/doc/questions.md#GeneratorBundle)