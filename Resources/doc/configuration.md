Configuration settings
----

Changes are that the defaults of this bundle are not to your liking. 
Perhaps you don't need to be bothered with 
certain questions for which your answer will always be the same.

In `config/services.yaml` or in `config/packages/generator.yaml`) you can
alter the following settings. Below the default settings are displayed.
```yaml
generator:
    default_bundle: null #using 'null' resolves to the 'App' namespace.
    ask_bundle: true  #use false if you're always using the default_bundle.
    ask_display_field: true #use false if you don't want to be bothered what field to use for __toString in entities.
    ask_entity_subdirectory: true #use false if you're not planning on using subdirectories for entities.
    default_entity_subdirectory: null #what default subdirectory should be used for entities?
    ask_traits: true  #use false if you don't want trait-questions.
    trait_options:
        # You can alter the trait namespace (must refer to a trait) if you want to use a different one.
        # If you don't want to use a trait-option, you can set the namespace to null.
        Blameable: 'Gedmo\Blameable\Traits\BlameableEntity'
        SoftDeleteable: 'Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity'
        Timestampable: 'Gedmo\Timestampable\Traits\TimestampableEntity'
        # You can add more custom traits here.
        # Note that trait_options won't matter if 'ask_traits' is set to false.
    
    # Overwriting attribute-settings are part of a special usecase.
    # You can quite easily choose defaults, but should only be used if you have a good understanding of how these settings work.
    # Please checkout the 'resources/config/services.yaml' of this bundle for more info about attribute-settings.
    attributes:
        nullable:
            default: false
        # You can add more attributes to your liking.   
        
    ask_datatable: true  #use false if you're never going to use datatables.
    use_datatable_default: true #Default option for whether or not datatables should be used.
    ask_voter: true  #use false if you don't want to be bothered with this question.
    use_voter_default: true #Default option for whether or not voters should be used.
    ask_controller_subdirectory: true  #use false if you don't want to be bothered with the question what subdirectory a controller should use
    default_controller_subdirectory: null  #what subdirectory controllers should use by default.
```

Please note that some options have dependencies on other bundles.
For instance,  `SecurityBundle` is required to use voters. If that
bundle isn't enabled, then no voter will be generated, no matter the configured
settings.
Likewise, datatables won't be generated without `SgDatatablesBundle` 
and traits won't be generated if their namespaces can't be resolved.
