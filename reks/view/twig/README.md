### Twig extension for REKS Framework

This twig extension is written to introduce features of REKS to Twig.



#### Adding inline js scripts
    {% js %}
    <script>
        alert("Hello World!");
    </script>
    {% endjs %s}



#### Adding css files

    {% asset "css" "css/style.css" %}


#### Adding js files
    {% asset "js" "js/jquery.js" %}



#### Printing compiled assets 

Print all the js files added.

    {{scripts('js')}}

Print all the css files.

    {{scripts('css')}}

Prints meta tags ( should be placed between <head></head> tags. ) This can also output the title tag.

    {{scripts('head')}}




#### Outputing a language variable

    {{lang('lang_var')}}


#### Printing images / assets

    {{asset('img/test.png')}}

