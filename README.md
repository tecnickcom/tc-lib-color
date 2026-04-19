# tc-lib-color

> PHP color toolkit for conversion and normalization across common color models.

[![Latest Stable Version](https://poser.pugx.org/tecnickcom/tc-lib-color/version)](https://packagist.org/packages/tecnickcom/tc-lib-color)
[![Build](https://github.com/tecnickcom/tc-lib-color/actions/workflows/check.yml/badge.svg)](https://github.com/tecnickcom/tc-lib-color/actions/workflows/check.yml)
[![Coverage](https://codecov.io/gh/tecnickcom/tc-lib-color/graph/badge.svg?token=l3UCVbShmc)](https://codecov.io/gh/tecnickcom/tc-lib-color)
[![License](https://poser.pugx.org/tecnickcom/tc-lib-color/license)](https://packagist.org/packages/tecnickcom/tc-lib-color)
[![Downloads](https://poser.pugx.org/tecnickcom/tc-lib-color/downloads)](https://packagist.org/packages/tecnickcom/tc-lib-color)

[![Donate via PayPal](https://img.shields.io/badge/donate-paypal-87ceeb.svg)](https://www.paypal.com/donate/?hosted_button_id=NZUEC5XS8MFBJ)

If this library helps your graphics workflow, please consider [supporting development via PayPal](https://www.paypal.com/donate/?hosted_button_id=NZUEC5XS8MFBJ).

---

## Overview

`tc-lib-color` provides utilities for parsing, converting, and formatting color values used in web and PDF rendering pipelines.

| | |
|---|---|
| **Namespace** | `\Com\Tecnick\Color` |
| **Author** | Nicola Asuni <info@tecnick.com> |
| **License** | [GNU LGPL v3](https://www.gnu.org/copyleft/lesser.html) - see [LICENSE](LICENSE) |
| **API docs** | <https://tcpdf.org/docs/srcdoc/tc-lib-color> |
| **Packagist** | <https://packagist.org/packages/tecnickcom/tc-lib-color> |

---

## Features

### Color Models
- RGB/RGBA and hexadecimal color handling
- HSL/HSLA and CMYK conversion workflows
- Grayscale and spot color support

### Integration Helpers
- CSS-ready color output
- PDF-oriented color conversion helpers
- Named web color lookup and normalization

---

## Requirements

- PHP 8.1 or later
- Extension: `pcre`
- Composer

---

## Installation

```bash
composer require tecnickcom/tc-lib-color
```

---

## Quick Start

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$web = new \Com\Tecnick\Color\Web();
$rgb = $web->getRgbObjFromHex('#336699');

echo $rgb->getCssColor();
```

See `example/index.php` for a complete conversion showcase.

---

## Development

```bash
make deps
make help
make qa
```

---

## Packaging

```bash
make rpm
make deb
```

For system packages, bootstrap with:

```php
require_once '/usr/share/php/Com/Tecnick/Color/autoload.php';
```

---

## Contributing

Contributions are welcome. Please review [CONTRIBUTING.md](CONTRIBUTING.md), [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md), and [SECURITY.md](SECURITY.md).

---

## Contact

Nicola Asuni - <info@tecnick.com>
