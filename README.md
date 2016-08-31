Objectify
=========

Build Web sites differently. Objectify presents an object-oriented view of Web site development designed to appeal to hardcore programmers and traditional Web designers alike.

This is Mocha, formerly known as Objectify. I have plans on changing the project name permanently, because "Objectify" has too many negative connotations and frankly, "Mocha" sounds cooler... and makes a friendly reference to a similar object-oriented data framework.

Everything is an Instance
-------------------------

Mocha is built on the principle that "everything is an instance". Instances have Attributes, which are themselves Instances (of the Attribute class), and they are connected between each other via Relationships, which are also Instances (of the Relationship class). And, of course, said "classes" are instances of the Class class; the only thing making them "classes" is the presence of the "has sub Class" relationship.

Hybrid Data Access
------------------

The first time a Mocha instance is accessed, it pulls its data from the database and caches it in memory. Further calls to the same object are based off the in-memory representation. Changes to the object are banged immediately into the database.

History
-------
* All "Properties" (Properties, InstanceProperties, and PropertyValues XQJS sections) have been removed in favor of Attributes and Relationships. EVERYTHING IS AN INSTANCE!

Things to Do
------------

* Page Components should be generated through `Page Component.has Build Element Method`, rather than relying on hardcoded implementations in multiple locations (PagePage, ExecuteInstancePage, and others actually hard-code multiple DIFFERENT and INCOMPATIBLE implementations of Page Components!)
   
* There should be no real distinction, as there has in the past, between "Objects" and "Instances" (of Objects). EVERYTHING IS AN INSTANCE!