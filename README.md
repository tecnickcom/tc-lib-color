# tc-lib-color

*Please consider supporting this project by making a donation to <paypal@tecnick.com>*

* **category**    Library
* **package**     \Com\Tecnick\Color
* **author**      Nicola Asuni <info@tecnick.com>
* **copyright**   2015-2015 Nicola Asuni - Tecnick.com LTD
* **license**     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
* **link **       https://github.com/tecnick.com/tc-lib-color

## Description

This library includes utility classes to get colors in various representation formats.

The initial source code has been extracted from TCPDF (<http://www.tcpdf.org>).


## Getting started

First, you need to install all development dependencies using [Composer](https://getcomposer.org/):

```bash
$ curl -sS https://getcomposer.org/installer | php
$ mv composer.phar /usr/local/bin/composer
```

This project include a Makefile that allows you to test and build the project with simple commands.
To see all available options:

```bash
make help
```

To install all the development dependencies:

```bash
make build_dev
```

## Running all tests

Before committing the code, please check if it passes all tests using

```bash
make qa_all
```
this generates the phpunit coverage report in target/coverage.
Please check if the tests are covering all code.

Generate the documentation:

```bash
make docs
```

Generate static analysis reports in target/report:

```bash
make reports
```

Other make options allows you to install this library globally and build an RPM package.
Please check all the available options using `make help`.


## Example

Examples are located in the `example` directory.

Start a development server using the command:

```
make server
```

and point your browser to <http://localhost:8000/index.php>


## Installation

Create a composer.json in your projects root-directory:

```json
{
    "require": {
        "tecnick.com/tc-lib-color": "master"
    }
}
```


## Developer(s) Contact

* Nicola Asuni <info@tecnick.com>
