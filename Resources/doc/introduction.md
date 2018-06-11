GeneratorBundle
===============

## Introduction

Many applications contain elements that are very consistent. This especially applies to 
CRUD applications. The [SensioGeneratorBundle](http://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html) 
was created to help you generate several files containing these elements with just a few simple commands. 
Unfortunately that bundle has been outdated for a while and as of Symfony 4 that bundle
is not supported. 

Symfony's alternative, the [MakerBundle](https://symfony.com/doc/1.0/bundles/SymfonyMakerBundle/index.html),
let's you generate some bare files, but they contain just too little.

To fill the gap this GeneratorBundle is created. It helps you quickly create (CRUD) applications 
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
- **Datatables** for showing table for multiple records, with sorting, search and pagination. To be used in the indexAction. 

By default, generated files will use php 7.1+ and take 
[Symfony's best practises](https://symfony.com/doc/4.2/best_practices/templates.html) into account
(there may be a few minor exceptions).

The many forks of SensioGeneratorBundle clarify that no generator will fit
everyone's exact needs.
**A lot** of effort is put in making this GeneratorBundle as extensible as possible, so that even if
this bundle doesn't fit your needs, it might still provide an excellent base for creating your
own generator.

Furthermore this Generator is highly customizable and lets you override template files to give
you control over the generated output.