GeneratorBundle
===============

**Introduction**
| [Getting started](getting_started.md#generatorbundle)
| [Usage](usage.md#generatorbundle)
| [Configuration](configuration.md#generatorbundle)
| [Templates](templates.md#generatorbundle)
| [Metadata](metadata.md#generatorbundle)
| [Questions](questions.md#generatorbundle)

## Introduction

Many applications contain elements that are very consistent. This especially applies to 
CRUD applications. The [SensioGeneratorBundle](http://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html) 
was created to help you generate several files containing these elements with just a few simple commands. 
Unfortunately that bundle has been outdated for a while and as of Symfony 4 that bundle
is not supported. 

Symfony's alternative, the [MakerBundle](https://symfony.com/doc/1.0/bundles/SymfonyMakerBundle/index.html),
is nice for a start, but (depending on what you want) it is too basic.

> **Note:** as of writing, the MakerBundle has gotten lots of extra's and is less bare than
it has been in its early stage. You might want to check it out. 
> The existing 'makers' (e.g. the MakeEntity) are still not suitable for extending though.  

To fill more needs for generating this GeneratorBundle is created. It helps you quickly create (CRUD) applications 
by letting you generate the following:

- **Entities**  (including relationships and validations)
- **Repositories** 
- **Controllers** for managing these entities
    - **indexAction** to overview multiple records
    - **showAction** for viewing details of a single record
    - **newAction** for adding a new record
    - **editAction** for changing an existing record
    - **deleteAction** for removing an existing record
- **FormTypes** which are needed in forms (used in newAction and editAction)
- **Template files** for each action you probably want to show a page:
    - **index.html.twig**
    - **show.html.twig**
    - **new.html.twig**
    - **edit.html.twig**
    - **delete.html.twig**
- **Voters** if you have some different roles to take into account. 

By default, generated files will use php 7.1+ and take 
[Symfony's best practises](https://symfony.com/doc/4.2/best_practices/templates.html) into account
(there may be a few minor exceptions).

The many forks of SensioGeneratorBundle clarify that no generator will fit
everyone's exact needs.
Extra effort is put in making this GeneratorBundle as extensible as possible, so that even if
this bundle doesn't fit your needs, it might still provide an excellent base for creating your
own generator.

Furthermore this Generator is highly customizable and lets you override template files to give
you control over the generated output.