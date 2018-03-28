Extending this bundle
----

This bundles provides a basis for generating your entities that might be just enough for all what
you need. 
However, changes are that you need more or perhaps you do not want more, but less questions to
be bothered with in your command line. 

Whatever your needs are, this bundle is created to be as extensible as possible. 

* No privates  
    It's often considered good practise to make everything private unless you have specific reason not
    to. This may be true to certain scenario's, but often leads to annoyance for those who want to
    extends something or need to make small alterations. Ever needed to rewrite just one line of code, but
    ended up rewriting multiple classes just because something was private?
   
    It the arrogance to think to know what everything is ultimately being used for,
    which in case of libraries results in frustrations of others.
     
    Since this bundle is intended to be extensible and it is impossible to predict what everyone
    needs, nothing is made private.
     
* Services, tags, factories and interfaces  
Symfony offers a powerful way to inject services and classes. This bundle embraces that.
For the most basic changes, you'll probably only need to set a few configurations. Even
if you want to make more complex changes, you may only need to extend a class or implement an
interface. Thanks to dependency injection, using tags and the compiler pass, this bundle
can automatically detect which class belongs where. 

* MetaData, MetaMetaData, MetaMetaMetadata...   
Thinking about metaData could cause a headache, but it becomes surprisingly useful when just
about everything needs to be changeable. For example, you can define a custom attribute in your
services configuration, which you can later use in twig files without needing to change
anything else.

* Twig files  
The MetaEntity which will hold all data is passed to twig files that are used to render
the file. If you need, you can override any twig file, allowing you to manipulate the end result.

**NOTE:** with great freedom, comes great responsibility. This bundle allows you to overwrite
just about everything. Just because you can, does'nt mean you should. 
Obviously, if you want to make complex changes, you need comprehensive knowledge about this
bundle to know how changes will affect things.