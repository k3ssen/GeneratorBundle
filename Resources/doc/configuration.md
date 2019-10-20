GeneratorBundle
===============

 [Introduction](introduction.md#generatorbundle)
| [Getting started](getting_started.md#generatorbundle)
| [Usage](usage.md#generatorbundle)
| **Configuration**
| [Abstract classes](abstract_classes.md#generatorbundle)
| [templates](templates.md#generatorbundle)
| [Metadata](metadata.md#generatorbundle)
| [Questions](questions.md#generatorbundle)

## Configuration settings

No configuration is required, but changes are that the defaults of this bundle are not to your liking. 
Perhaps you don't need to be bothered with 
certain questions for which your answer will always be the same.

In `config/config.yml` or in `config/packages/generator.yaml`) you can
alter the following settings. Below the default settings are displayed.
```yaml
generator:
    default_bundle: null              #using 'null' resolves to the 'App' namespace.
    ask_bundle: true                  #use false if you're always using the default_bundle.
    ask_display_field: true           #use false if you don't want to be bothered what field to use for __toString in entities.
    ask_entity_subdirectory: true     #use false if you're not planning on using subdirectories for entities.
    default_entity_subdirectory: null #what default subdirectory should be used for entities?
    ask_traits: true                  #use false if you don't want trait-questions.
    trait_options: 
        Blameable:
            ask: true                 #use false to not ask for Blameable
            default: true             #default choice-value (also used when aks is set to false)
            namespace: 'Gedmo\Blameable\Traits\BlameableEntity'
        SoftDeleteable:
            ask: true
            default: true
            namespace: 'Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity'
        Timestampable:
            ask: true
            default: true
            namespace: 'Gedmo\Timestampable\Traits\TimestampableEntity'
        # You can add more traits if you want.
    
    # Overwriting attribute-settings are part of a special usecase.
    # You can quite easily choose defaults, but should only be used if you have a good understanding of how these settings work.
    # Please checkout the 'resources/config/services.yaml' of this bundle for more info about attribute-settings.
    attributes:
        nullable:
            default: false
        # You can add custom attributes   
    
    templates_directory: null                #Define directory for twig-templates (null results in 'templates' directory in your projectroot).
    templates_file_extension: 'html.twig'    #Define file extensions to be used for rendered template files.
    ask_use_voter: true                      #use false if you don't want to be bothered with this question.
    use_voter_default: true                  #Default option for whether or not voters should be used.
    check_security_bundle_enabled: true      #Option for whether or not should be checked if SecurityBundle is enabled for generating a voter.
    ask_use_write_actions: true              #use false if you don't want to be bothered with this question.
    use_write_actions_default: true          #Default option for whether or not write actions (new, edit, delete) should be used.
    ask_controller_subdirectory: true        #use false if you don't want to be bothered with the question what subdirectory a controller should use.
    controller_subdirectory_default: null    #what subdirectory controllers should use by default.
    ask_use_datatable: true                  #use false if you don't want to be bothered with this question.
    use_datatable_default: true              #Default option for whether or not datatables should be used.
    check_sg_datatables_bundle_enabled: true #Option for whether or not should be checked if SgDatatablesBundle is enabled for generating a datatable.
```

Note that some options have dependencies on other bundles.
For instance,  `SecurityBundle` is required to use voters. If that
bundle isn't enabled, then no voter will be generated, no matter the configured
settings.
Likewise, traits won't be generated if their namespaces can't be resolved.
