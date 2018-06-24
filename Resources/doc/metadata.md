GeneratorBundle
===============

 [Introduction](Introduction.md#generatorbundle)
| [Getting started](getting_started.md#generatorbundle)
| [Usage](usage.md#generatorbundle)
| [Configuration](configuration.md#generatorbundle)
| [Templates](templates.md#generatorbundle)
| **Metadata**
| [Questions](questions.md#generatorbundle)

## Metadata

To store and build information about the entity metadata classes are used. 
Though doctrine already has classes for metadata, this bundle uses different classes to fit the
needs for our generator.

The structure for our meta data is as follows:

* **MetaEntity**  
  Ultimately, all metadata will be stored in the MetaEntity class. 
    * **MetaAnnotation**  
       Annotation objects will be used for the entity's class annotation.
       For example, the '@ORM\Table(name="tablename")' will be defined in
       a metaAnnotation object.
    * **MetaTrait**  
       Optionally traits can be used for the entity. Definitions for traits
       are stored in metaTrait objects.  
    * **MetaInterface**  
    Information about which interfaces this entity implements.    
    * **MetaProperty**  
       Information about fields/columns are stored in MetaProperty objects.
        * **MetaAttributes**  
        Information about the property, such as its name, type, length or whether it's nullable
        or not is stored in MetaAttribute objects.
        * **MetaValidations**  
        Validator-constraint, used as @Assert annotations, are defined in the MetaValidation.

Using this structure the entity can be build to very specific needs.

To make sure this metadata can be configured in whichever way you need, each meta data class
is created through a corresponding factory. 
Furthermore, the metaAttributes which ultimately hold most of the actual values, can be defined
in the configuration `entity_generator: {attributes: {...}}`.

### Extending metadata classes

Most classes (MetaEntity, MetaAttribute, MetaAnnotation, MetaTrait and MetaValidation)
can be overwritten by simply implementing the same interface, or extending the class you need to override.

For example, to overwrite the MetaEntity, you'll only need to create your own MetaEntity class
that implements the MetaEntityInterface. Alternatively you can extend
the MetaEntity.

> **Note:** a lot depends on the metadata. To overwrite metadata, 
you need to make sure that you really know what you're doing. 
> You might need extensive knowledge about the code in  the GeneratorBundle for this.

### Extending MetaProperties

While there's only one class for most metadata, there are several MetaProperty classes. Simply extend one
of the classes to overwrite them. Alternatively you can implement the corresponding Interface. 

MetaProperties are in fact not overwritten by their interfaces or class names, but by the value that
the static `getOrmType` methods returns. In other words: there can only be one
MetaProperty for each OrmType. Therefore, by defining a MetaProperty with an OrmType
that has already been defined, you overwrite the existing definition.

Consequently, you can add new MetaProperties by returning different OrmTypes. Just make sure the MetaPropertyInterface is implemented
and your class will automatically be injected. 
The simplest way to get started is by extending the `AbstractMetaProperty` or the
`AbstractPrimitiveMetaProperty`.

### Extending MetaAttribute

When you want to change or add a MetaAttribute, you'll probably want to use the configuration
setting `generator.attributes` instead of messing around with the class.

For example, if you want to add a new attribute called `private_only`, you could add the
following to your config:

    generator:
        attributes:
            private_only:
                type: 'bool'
                default: false
                meta_properties: ['K3ssen\GeneratorBundle\MetaData\Property\RelationMetaPropertyInterface']

The attribute-settings are composed of the following options:

- **type**: must be either 'string', 'int', 'bool', 'array' or 'object'
- **meta_properties**: array of interfaces. Properties that implement one of these interfaces will use this attribute.
- **default**: a default value to be used. If no default is provided, then `null` will be the default.
- **question**: reference to any question that implements 'AttributeQuestionInterface'. 
    - If no question is provided, the 'BasicAttributeQuestion' will be used.
    - If `null` is provided, no question will be used.
- **requirement_expression**: an expression (ExpressionLanguage is used for evaluation) to decide if this attribute should be
    asked or not. (this is only used in BasicAttributeQuestion)
    
    
The default_attributes defined in `Resources/config/services.yaml` of this bundle can be
overridden in `generator.attributes`. However, attribute names and types
cannot be overridden.