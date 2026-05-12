<?php

declare(strict_types=1);

/**
 * Css.php
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link      https://github.com/tecnickcom/tc-lib-color
 *
 * This file is part of tc-lib-color software library.
 */

namespace Com\Tecnick\Color;

use Com\Tecnick\Color\Exception as ColorException;

/**
 * Com\Tecnick\Color\Css
 *
 * Css Color class
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
abstract class Css
{
    abstract public function normalizeValue(mixed $value, int $max): float;

    /**
     * Get the color object from acrobat Javascript syntax
     *
     * @param string $color color specification (e.g.: ["RGB",0.1,0.3,1])
     *
     * @throws ColorException if the color is not found
     */
    protected function getColorObjFromJs(string $color): ?\Com\Tecnick\Color\Model
    {
        $col = [];
        $colorType = $color[2] ?? null;
        if ($colorType === null || !str_contains('tgrc', $colorType)) {
            throw new ColorException('invalid javascript color: ' . $color);
        }

        switch ($color[2]) {
            case 'g':
                $rex = '/[\[][\"\']g[\"\'][\,]([0-9\.]+)[\]]/';
                if (\preg_match($rex, $color, $col) !== 1) {
                    throw new ColorException('invalid javascript color: ' . $color);
                }

                return new \Com\Tecnick\Color\Model\Gray([
                    'gray' => $col[1] ?? '0',
                    'alpha' => 1,
                ]);
            case 'r':
                $rex = '/[\[][\"\']rgb[\"\'][\,]([0-9\.]+)[\,]([0-9\.]+)[\,]([0-9\.]+)[\]]/';
                if (\preg_match($rex, $color, $col) !== 1) {
                    throw new ColorException('invalid javascript color: ' . $color);
                }

                return new \Com\Tecnick\Color\Model\Rgb([
                    'red' => $col[1] ?? '0',
                    'green' => $col[2] ?? '0',
                    'blue' => $col[3] ?? '0',
                    'alpha' => 1,
                ]);
            case 'c':
                $rex = '/[\[][\"\']cmyk[\"\'][\,]([0-9\.]+)[\,]([0-9\.]+)[\,]([0-9\.]+)[\,]([0-9\.]+)[\]]/';
                if (\preg_match($rex, $color, $col) !== 1) {
                    throw new ColorException('invalid javascript color: ' . $color);
                }

                return new \Com\Tecnick\Color\Model\Cmyk([
                    'cyan' => $col[1] ?? '0',
                    'magenta' => $col[2] ?? '0',
                    'yellow' => $col[3] ?? '0',
                    'key' => $col[4] ?? '0',
                    'alpha' => 1,
                ]);
        }

        // case 't'
        return null;
    }

    /**
     * Get the color object from a CSS color string
     *
     * @param string $type  color type: t, g, rgb, rgba, hsl, hsla, cmyk
     * @param string $color color specification (e.g.: rgb(255,128,64))
     *
     * @throws ColorException if the color is not found
     */
    protected function getColorObjFromCss(string $type, string $color): ?\Com\Tecnick\Color\Model
    {
        switch ($type) {
            case 'g':
                return $this->getColorObjFromCssGray($color);
            case 'rgb':
            case 'rgba':
                return $this->getColorObjFromCssRgb($color);
            case 'hsl':
            case 'hsla':
                return $this->getColorObjFromCssHsl($color);
            case 'cmyk':
            case 'cmyka':
                return $this->getColorObjFromCssCmyk($color);
        }

        // case 't'
        return null;
    }

    /**
     * Get the color object from a CSS Gray color string
     *
     * @param string $color color specification (e.g.: g(128))
     *
     * @throws ColorException if the color is not found
     */
    private function getColorObjFromCssGray(string $color): \Com\Tecnick\Color\Model\Gray
    {
        $col = [];
        $rex = '/[\(]([0-9\%]+)[\)]/';
        if (\preg_match($rex, $color, $col) !== 1) {
            throw new ColorException('invalid css color: ' . $color);
        }

        return new \Com\Tecnick\Color\Model\Gray([
            'gray' => $this->normalizeValue($col[1] ?? '0', 255),
            'alpha' => 1,
        ]);
    }

    /**
     * Get the color object from a CSS RGB/RGBA color string
     *
     * @param string $color color specification (e.g.: rgb(255,128,64))
     *
     * @throws ColorException if the color is not found
     */
    private function getColorObjFromCssRgb(string $color): \Com\Tecnick\Color\Model\Rgb
    {
        $col = [];
        $rex = '/[\(]([0-9\%]+)[\,]([0-9\%]+)[\,]([0-9\%]+)[\,]?([0-9\.]*)[\ )]/';
        if (\preg_match($rex, $color, $col) !== 1) {
            throw new ColorException('invalid css color: ' . $color);
        }

        $alpha = $col[4] ?? '';

        return new \Com\Tecnick\Color\Model\Rgb([
            'red' => $this->normalizeValue($col[1] ?? '0', 255),
            'green' => $this->normalizeValue($col[2] ?? '0', 255),
            'blue' => $this->normalizeValue($col[3] ?? '0', 255),
            'alpha' => $alpha !== '' ? $alpha : 1,
        ]);
    }

    /**
     * Get the color object from a CSS HSL/HSLA color string
     *
     * @param string $color color specification (e.g.: hsl(120,100%,50%))
     *
     * @throws ColorException if the color is not found
     */
    private function getColorObjFromCssHsl(string $color): \Com\Tecnick\Color\Model\Hsl
    {
        $col = [];
        $rex = '/[\(]([0-9\%]+)[\,]([0-9\%]+)[\,]([0-9\%]+)[\,]?([0-9\.]*)[\ )]/';
        if (\preg_match($rex, $color, $col) !== 1) {
            throw new ColorException('invalid css color: ' . $color);
        }

        $alpha = $col[4] ?? '';

        return new \Com\Tecnick\Color\Model\Hsl([
            'hue' => $this->normalizeValue($col[1] ?? '0', 360),
            'saturation' => $this->normalizeValue($col[2] ?? '0', 1),
            'lightness' => $this->normalizeValue($col[3] ?? '0', 1),
            'alpha' => $alpha !== '' ? $alpha : 1,
        ]);
    }

    /**
     * Get the color object from a CSS CMYK color string
     *
     * @param string $color color specification (e.g.: rgb(255,128,64))
     *
     * @throws ColorException if the color is not found
     */
    private function getColorObjFromCssCmyk(string $color): \Com\Tecnick\Color\Model\Cmyk
    {
        $col = [];
        $rex = '/[\(]([0-9\%]+)[\,]([0-9\%]+)[\,]([0-9\%]+)[\,]([0-9\%]+)[\,]?([0-9\.]*)[\ )]/';
        if (\preg_match($rex, $color, $col) !== 1) {
            throw new ColorException('invalid css color: ' . $color);
        }

        $alpha = $col[5] ?? '';

        return new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => $this->normalizeValue($col[1] ?? '0', 100),
            'magenta' => $this->normalizeValue($col[2] ?? '0', 100),
            'yellow' => $this->normalizeValue($col[3] ?? '0', 100),
            'key' => $this->normalizeValue($col[4] ?? '0', 100),
            'alpha' => $alpha !== '' ? $alpha : 1,
        ]);
    }
}
