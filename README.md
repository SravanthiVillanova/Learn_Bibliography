VuBib is a project to modernize our old bibliography management software. Old bibliography management software also known as "Finding Augustine"/
"Panta Rhei" contains works about Saint Augustine. Finding Augustine is a web application that has details of works about saint augustine, 
publishers, agents, languages of these works. Finding Augustine application relies on Smarty Templates and a variety of 
PEAR libraries for its functionality which are aging out of favor. 

VuBib is built using the following technologies:
PHP 5.6
zend-servicemanager dependency container
FastRoute router
Twig template engine
Whoops Error Handler
Zend expressive framework
Bootstrap for HTML, CSS and JS

Not many functional changes were made in VuBib except for removing few unnecessary and redundant things like 'Parent lookup' in Work > New, Edit 
and 'Parent' in Classification > Edit. Database constraints are enforced wherever necessary.

Installation:
-------------
Install this project with composer:

$ composer create-project VuBib <project-path>
After choosing and installing the packages you want, go to the <project-path> and start PHP's built-in web server to verify installation:

Download/Clone the project from github
Goto prj directory path, type composer install
