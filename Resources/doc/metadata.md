MetaData
----

To store and build information about the entity to be created MetaData classes are used. 
Though doctrine already has classes for MetaData, this bundle uses different classes to fit the
needs for our generator.

The structure for our meta data is as follows:

* **MetaEntity**  
  All meta-data will be stored in the MetaEntity class. 
    * **MetaProperty**  
       Information about fields/columns are stored in MetaProperty objects.
        * **MetaAttributes**  
        Information about the property, such as its name, type, length or whether it's nullable
        or not is all stored in MetaAttributes.
        * **MetaValidations**  
        Validator-constraint, used as @Assert annotations, are defined in the MetaValidation.

Using this structures, the meta data is very dynamic, so it can be configured to very specific
needs.

To make sure this meta data can be configured in whichever way you need, each meta data class
is created through a corresponding factory. 
Furthermore, the metaAttributes which ultimately hold most of the actual values, can be defined
in the configuration `entity_generator: {attributes: {...}}`.

### Extending MetaEntity

Overwriting or extending the MetaEntity simply can be done by either implementing
the MetaEntityInterface or by extending the already existing MetaEntity class. 
That's all there's to it! The compiler pass wil recognize the interface and makes sure the
MetaEntityFactory uses this class.

Note: It is not recommended to mess with the MetaEntity unless you really know what you're doing or
if it's only a very small addition you need to make. 

### Extending MetaProperties

While there's only one MetaEntity, there are several MetaProperty classes. Simply extend one
of the classes or implement one of their corresponding interfaces to extend or overwrite them. 

You can also add new MetaProperty classes: just make sure the MetaPropertyInterface is implemented
and your class will automatically be injected. 
The simplest way to get started is by extending the `AbstractMetaProperty` or the
`AbstractPrimitiveMetaProperty`.

### Extending MetaAttribute

The MetaAttribute class can be extended or overwritten by implementing the MetaAttributeInterface.

When you want to change or add a MetaAttribute, you'll probably want to use the configuration
setting `entity_generator.attributes` instead of messing around with the class.

For example, if you want to add a new attribute called `private_only`, you could add the
following to your config:

    entity_generator:
        attributes:
            private_only:
                type: 'bool'
                default: false

##### TODO: refer to more details