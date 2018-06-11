GeneratorBundle
===============

## Command questions

### Simple commands
The CrudCommand, RepositoryCommand and TemplatesCommand are fairly simple
and straightforward. If you want to make alterations, just extend these
commands and implement the methods in the way you deem necessary.

### EntityCommand
The EntityCommand is a whole different story, since building/generating an
entity is not a simple matter of a few yes/no questions. 

To build entities, metadata with information about this entity is used (see the [metadata documentation](metadata.md) for more info) 
and to collect information for metadata questions are used in the command.

Since there are quite a few questions to be asked for collecting this information,
the questions are refactored into several classes that can be divided into 
a few categories:

* **EntityQuestions:** these are questions that are directly related to the Entity itself, such
as the entity name and what properties to use.  
* **PropertyQuestions:** for each property we want to add, there are several questions
to be asked as well, such as the property name and type and what validations to use.
* **AttributeQuestions:** depending on the property type, there are different questions to
be asked about it's attribute, like length, id, nullable, etc.

An interface exists for each of these questions:
* EntityQuestionInterface
* PropertyQuestionInterface
* AttributeQuestionInterface

By implementing one of these interfaces you can add your own question-classes for
handling questions of your own.

The already existing question classes are all services, which you can override
in your `config/services.yaml`. Of course be wary of not breaking anything by doing
so.

