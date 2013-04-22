# Deprecated

See [Minibase](https://github.com/peec/minibase) for a much better framework. 

# Reks

Super lightweight OOP MVC framework for PHP.


##### Documentation

- [Documentation on PKJ.NO](http://pkj.no/manual/reks)



##### Features

- Module support ( application can depend on applications, fully recursive )
- Applications are fully separated. No global constants, no static code, just clean objects.
- Using known design patterns.
- Namespaced.
- Easy to get started
- Routes and reverse routing.
- Javascript routes (This is really cool ! )
- Support for our own lightweight DB Abstract layer (PDO) and Doctrine 2 ORM, and easy to use other DBAL/ORM ( like redbreans, propel etc )
- Loose coupled code.


###### Why REKS

It exists dozens of web application frameworks for PHP already, why did we create REKS framework and why should you use it for future projects?

REKS is a extremely modular built framework, especially the module system. Think about if you where about to create a blog system, a blog system might have these components (modular components):

- User authentication system
- Content system for the blog
- Commenting system

All these 3 systems can be split in to 3 modules. You might think that this is hard to split up and that takes time,
but in REKS its not worse then copying the applicable routes, configs, controllers, views and models + libraries etc into a new application
folder and include the module in the configuration of the parent application.




### Some cool examples.
REKS is cool, it's api is really, REALLY simple and easy to use.



###### File uploading

	$file = $this->request->file->get('myfile');
	if ($file){
		$file->validator->isImage()->extensions(array('gif', 'png'));
		$file->upload("{$this->app->APP_PATH}/cache/{$file->getName()}");
	}


###### Ajax requests.

	
	jsRoutes.NewsController.index().ajax(function(){
		success: function(data){
			alert("Successful ajax req.");
		}
	});



###### Form generation (PHP templating engine)


	<?php echo $form = $view->createForm('myForm') ?>
	<?php echo $form->input('text', 'search')?><button type="submit">Search!</button>
	<?php echo $form->close() ?>
	

###### CSS / JS - One single file automatically.
In production, we know that you want css files and javascript files compiled to one single file, so, just use our simple API (Twig).


	{% asset "css" "style.css" %}
	{% asset "js" "jquery.css" %}
	{% asset "css" "css/ui.css" %}
	

And in header between `<head>` tags.

	{{scripts("css")}}


And in the footer, anywhere.

	{{scripts("js")}}
	
