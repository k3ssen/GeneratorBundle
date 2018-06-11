GeneratorBundle
===============

## Usage

This bundle consist of two main parts:
1) Entity generation
2) Crud generation based on entity

### Entity Generation `generator:entity:create`

To start generating an entity, use `php bin/console generator:entity:create` in your console. 

The interactive command will ask you the following information:
- entity name
- what traits to use (only if there are traits configured in generator-setting)
- what properties to add:
    - property name
    - property type
    - attributes, such as 'length', 'nullable', 'unique'
    (questions are based on type)
    - what validations to use
- which property to use in '__toString' method.    

The interactive command is built in such way that it lets you edit your choices
if you made any mistake.

Additionally, you can provide the `--savepoint` option to continue editing the
last entity you were building. This can be convenient when you accidentally 
close the command (by hitting something like ctrl+c or closing the console).

#### Relationships

The property-types you can use include relationships, such as ManyToOne.

A required part of relationships is that you provide an targetEntity.

The command will provide you existing options, but also allows you to provide an
entity that does'nt exist yet. 

- If you provide a non-existing targetEntity, the command will also generate this targetEntity.
- If an existing targetEntity is provided, the command will update that
targetEntity to correspond the 'inversedBy' or 'mappedBy' attributes.

#### Altering an existing entity `generator:entity:alter`

Using `php bin/console generator:entity:alter` the generator enables you to
read an existing entity and make changes to it, such as adding or removing fields.

**Note:** Reading an entity can only be done to some extend. Custom methods
won't be processed and are sure to be lost when regenerating the entity using this
command.

It is only recommended to use this command if you
need to alter an entity that has just been generated and you made no 
custom changes to, for example when an new targetEntity has been generated.

#### Appending fields to an existing entity `generator:entity:append`

Using `php bin/console generator:entity:append` the generator allows you
to add properties to an existing entity. 

Unlike altering an existing entity, this won't result in lost of existing content like
custom methods; it merely adds properties.
Consequently, this command won't enable you to make alterations to an
existing entity.

**Note:** Since using this command is safe compared to `generator:entity:alter`, it
is recommended to use this command for entities that you've made custom
changes to.


### CRUD Generation `generator:crud`

By using `php bin/console generator:crud` the generator will create files that help you setup 
the application.

The interactive command will ask you the following information:
- what entity to use
- a subdirectory for you controller (optional)
- if you want to use write actions (new, edit, delete)
- if you want to generate (and use) a voter class
- if you want to generate (and use) a datatable class


The generator will then create the following:
- `[bundlePath]`/Controller/`[subdirectory]`/`[EntityName]`**Controller.php**
- `[bundlePath]`/Form/**`[EntityName]`Type.php** (only if 'write actions' is true)
- `[bundlePath]`/Datatable/`[EntityName]`**Datatable.php** (only is 'using datatable is true)
- `[bundlePath]`/Security/`[EntityName]`**Voter.php** (only if 'using voter' is true)
- templates/[subdirectory]/`[entity_name]`
    - **index.html.twig**
    - **view.html.twig**
    - **new.html.twig** (only if 'write actions' is true)
    - **edit.html.twig** (only if 'write actions' is true)
    - **delete.html.twig** (only if 'write actions' is true)

`[bundlePath]` is the path to the same bundle of the entity or simple the 'src' directory
if no bundle is used.  
`[subdirectory]` is the subdirectory provided for the controller. If none is provided, then
there will be no subdirectory.  
`[EntityName]` the class name of the selected entity.