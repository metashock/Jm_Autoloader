# J@m; Autoloader

Seit PHP5.? die Möglichkeit einführte, Klassen dynamisch zur Laufzeit zu laden, ist dies eine vielgenutzte Funktionalität und Kernbestandteil vieler PHP Frameworks. Auch Jam benutzt Autoloading um Klassen zur Laufzeit zu laden. Daher ist Jm_Autoload auch Kernbestandteil von Jm. Er kann natürlich auch unabhängig von Jam benutzt werden.

Das von den PHP Entwicklern Autoloading genannte feature bietet die folgenden Vorteile:

- zahllose teils komplizierte require_once statements entfallen
- Wenn sich der Programmierer ein einheitliches Schema bei der Benahmung von Klassendateien hält, kann er komplett auf `include` oder `require` Anweisungen zum Laden von Klassen verzichten.


## Installation

To install Jm_Autoloader you can use the PEAR installer or get a tarball and install the files manually.

___
### Using the PEAR installer

If you haven't discovered my pear channel yet you'll have to do it. Also you should issue a channel update:

    pear channel-discover metashock.de/pear
    pear channel-update metashock

After this you can install Jm_Autoloader. Note that if you installed one of the other Jm packages before it is likely that you have already installed Jm_Autoloader as its a dependency of the most jAm packages. The following command will install the lastest stable version:

    pear install -a metashock/Jm_Autoloader

If you want to install a specific version or a beta version you'll have to specify this version on the command line. For example:

    pear install -a metashock/Jm_Autoloader-0.3.0

___
### Manually download and install files

Alternatively, you can just download the package from http://www.metashock.de/pear and put into a folder listed in your include_path. Please refer to the php.net documentation of the include_path directive.


## Documentation


### How to name classes?

Currently the J@m; Autoloader only supports the PEAR style naming scheme that uses underscores to separate between package names and classnames. This is the scheme used to code J@m; itself.

For example the Autolader expects the following class in a file name `View/Html/Table.php`

```php
    class View_Html_Table {
       // ...
```

If you use the default configuration, Jm_Autoloader will search in paths listed in `ini_get('include_path');`.

