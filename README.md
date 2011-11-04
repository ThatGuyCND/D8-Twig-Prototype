Prontotype
==========

Prontotype is a lightweight, server-side framework to help you quickly build data-driven, interactive prototypes.

Prontotype lets you:
--------------------

* Quickly build linked, multi-page prototypes with 'realistic' URLs
* Define reusable chunks of HTML to make prototype-wide updates quick and easy
* Separate your layouts from your content using powerful template inheritance
* Mock-up everything from simple login behaviours to complex role-based authentication scenarios
* Add contextual notes to pages and content blocks that you can easily show/hide
* Use YAML or CSV files to define data structures that will be accessible throughout your prototype
* Write CSS using LESS and have it automatically compiled for use in your protoype
* Make use of a library of pre-build components to rapidly build your interfaces
* Use whatever frontend prototyping framework you like (or roll it all by hand)
* And plenty more&hellip;

What you'll need to run Prontotype:
--------------------

* Linux-based web server
* Apache with mod_rewrite enabled
* PHP version 5.2.4+

Installation
------------

1. Download and unzip the latest version ([or clone it from the Github repo](https://github.com/allmarkedup/prontotype))
2. Move the files to the web root of your server
3. Change the filename of the `htaccess.txt` file to `.htaccess` (this may make it an 'invisible' file, depending on your OS)
4. Make sure the cache folder (found at `/system/_cache`) is writeable (i.e. octal permissions of 777)

Documentation
-------------

**Coming soon.** When you first install Prontotype it will install a few example pages that will help you get started, but full docs are on their way.

Prontotype makes heavy use of [Twig](http://twig.sensiolabs.org/) and [YAML](http://yaml.org/start.html) so becoming familiar with them will help you greatly in mastering everything Prontotype has to offer.