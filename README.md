Prontotype
==========

Prontotype is a lightweight, server-side framework to help you quickly build interactive, data-driven HTML prototypes.

Prontotype lets you:
--------------------

* Quickly build linked, multi-page prototypes with realistic URLs
* Define reusable chunks of HTML to make prototype-wide updates quick and easy
* Separate your layouts from your content using powerful template inheritance
* Mock-up everything from simple login behaviours to complex role-based authentication scenarios
* Use YAML or CSV files to define data structures that will be accessible throughout your prototype
* Write CSS using LESS and have it automatically compiled for use in your protoype
* Give your pages IDs so you can move them around easily without breaking links
* Use whatever frontend prototyping framework you like (Twitter Bootstrap is included but anything can be used)</li>
* Auto-generate navigation, forms and more using in-built macros
* And plenty more!


What you'll need to run Prontotype:
--------------------

* Linux-based web server
* A web server like Apache (optionally with mod_rewrite or equivalent enabled) or Nginx
* PHP version 5.3.1+

Installation
------------

1. Download and unzip the latest version ([or clone it from the Github repo](https://github.com/allmarkedup/prontotype))
2. Move the files to the web root of your server
3. Optionally tweak some of the default configuration settings (but you probably don't need to!)

Documentation
-------------

In-progress documentation is available at [http://prontotype.allmarkedup.com](http://prontotype.allmarkedup.com)

Prontotype makes heavy use of [Twig](http://twig.sensiolabs.org/) and [YAML](http://yaml.org/start.html) so becoming familiar with them will help you greatly in mastering everything Prontotype has to offer.

In the works...
---------------

Prontotype is still very young! The following is a list of things (in no particular order) that are on the to-do list for the near-future:

* An **example site** to better show off all the features of Prontotype.
* **Extension system** to allow you to model more complex behaviours that need server-side logic.
* **Page scraping capability** so that you can populate pages in your prototype with content dynamically scraped from pages of sites.
* **XML/JSON feed parser** to allow pages to be dynamically populated with data that exists as JSON or XML feeds.
* The ability to **specify contextual notes** and have their display state toggled easily on and off.
* **WURFL** integration to allow for prototyping of server-side device detection.
* **Coffeescript compiliation** to work in a similar way to the LESS compilation does.
* **More macros** for Bootstrap components, dummy images services etc.
* **Unit tests** to make sure everything is working ok!





