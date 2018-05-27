GeneratorBundle
=====================

Symfony 4 bundle for quickly generating/prototyping a CRUD application:
* Generate entities using interactive commands.  
  Included features:
    - Define properties with their types (string, int, array, etc) and attributes (length, nullable, unique, etc) 
    - Types also include relationship-types
        - ManyToOne, OneToMany, ManyToMany, OneToOne
        - Adds fields to mapped or inversed targetEntity
        - creates target entity if it does'nt exist yet.
    - Add validations (annotations like `@Assert\Length`)
    - Add fields to existing entities
    - Read existing entities and interactively add/edit/remove properties.
* Generate Controller and templates file.
* Generate FormType for controller actions (new, edit, delete)
* Optionally use/generate Datatables
* Optionally use/generate Voters

This bundle is highly customizable for simple usage:
- Enable/disable questions that you do or don't need  
- Specify defaults
- All files are generated through twig files, which you can override by
using identical files in `/templates/bundles/GeneratorBundle/...`
To make things simpler, you can use the command `generate:templates` to
add files to this directory that extend the generatorBundle. 

For complex usage, the bundle is built in a very extensible way, allowing
you to override nearly every single file independently.