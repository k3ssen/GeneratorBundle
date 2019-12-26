GeneratorBundle
=====================

[![Build Status](https://travis-ci.com/k3ssen/GeneratorBundle.svg?branch=master)](https://travis-ci.com/k3ssen/GeneratorBundle)

A Symfony bundle for quickly generating/prototyping a CRUD application. Compatible with
Symfony 3.4, Symfony 4 and Symfony 5.

This bundle is similar to Symfony's [MakerBundle](https://github.com/symfony/maker-bundle),
except that this bundle is built to be more extensible.

Features:
* Generate entities using interactive commands:
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
    - AbstractController (generated once to be used by Controllers)
    - Template (twig) files
    - Form
    - Voter (optional)
    - AbstractVoter (generated once to be used by Voters)
    - Datatable (optional)
    - AbstractDatatable (generated once to be used by Datatables)

This bundle is highly customizable:
- Files are generated through twig files, which you can override by
using identical files in `/templates/bundles/GeneratorBundle/...`.
    - To make things simpler, you can use the command `generate:templates` to 
have this done automatically for you.
    - By using the `meta_entity` and `generate_options` parameters in twig you'll have
lots of options to make your files perfectly suited for your application.
- Enable/disable questions that you do or don't need  
- Specify defaults
- For complex usage, the bundle is built in a very extensible way, allowing
you to override nearly everything independently.


## Documenation

* [Introduction](Resources/doc/introduction.md#GeneratorBundle)
* [Getting started](Resources/doc/getting_started.md#GeneratorBundle)
* [Usage](Resources/doc/usage.md#GeneratorBundle)
* [Configuration](Resources/doc/configration.md#GeneratorBundle)
* [Abstract classes](Resources/doc/abstract_classes.md#generatorbundle)
* [Templates](Resources/doc/templates.md#GeneratorBundle)
* [Metadata](Resources/doc/metadata.md#GeneratorBundle)
* [Questions](Resources/doc/questions.md#GeneratorBundle)