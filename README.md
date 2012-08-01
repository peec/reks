REKS FRAMEWORK (PHP)
====

Super lightweight OOP MVC framework for PHP.


##### Documentation



##### Features

- Module support ( applications in applications )
- Applications are fully separated. No global constants.
- Using known design patterns.
- Namespaced.
- Easy to get started
- Routes and reverse routing.
- Support for our own lightweight DB Abstract layer (PDO) and Doctrine 2 ORM, and easy to use other DBAL/ORM ( like redbreans, propel etc )



###### Why REKS

It exists dozens of web application frameworks for PHP already, why did we create REKS framework and why should you use it for future projects?

REKS is a extremely modular built framework, especially the module system. Think about if you where about to create a blog system, a blog system might have these components (modular components):

- User authentication system
- Content system for the blog
- Commenting system

All these 3 systems can be split in to 3 modules. You might think that this is hard to split up and that takes time,
but in REKS its not worse then copying the applicable routes, configs, controllers, views and models + libraries etc into a new application
folder and include the module in the configuration of the parent application.
