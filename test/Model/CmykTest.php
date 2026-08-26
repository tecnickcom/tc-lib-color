<?php

declare(strict_types=1);

/**
 * CmykTest.php
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 *
 * This file is part of tc-lib-color software library.
 */

namespace Test\Model;

use Test\TestUtil;

/**
 * Cmyk Color class test
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class CmykTest extends TestUtil
{
    protected function getTestObject(): \Com\Tecnick\Color\Model\Cmyk
    {
        return new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0.666,
            'magenta' => 0.333,
            'yellow' => 0,
            'key' => 0.25,
            'alpha' => 0.85,
        ]);
    }

    public function testGetType(): void
    {
        $cmyk = $this->getTestObject();
        $type = $cmyk->getType();
        $this->assertSame('CMYK', $type);
    }

    /**
     * Components are clamped into [0..1] on construction.
     */
    public function testConstructorClampsComponents(): void
    {
        $over = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 2,
            'magenta' => 1.5,
            'yellow' => 0.5,
            'key' => 100,
            'alpha' => 5,
        ]);
        $this->assertSame(
            [
                'C' => 1.0,
                'M' => 1.0,
                'Y' => 0.5,
                'K' => 1.0,
                'A' => 1.0,
            ],
            $over->getArray(),
        );

        $under = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => -0.5,
            'magenta' => -1,
            'yellow' => 1,
            'key' => -100,
            'alpha' => -2,
        ]);
        $this->assertSame(
            [
                'C' => 0.0,
                'M' => 0.0,
                'Y' => 1.0,
                'K' => 0.0,
                'A' => 0.0,
            ],
            $under->getArray(),
        );
    }

    /**
     * Omitted components default to 0.0, except alpha which defaults to opaque.
     */
    public function testConstructorDefaults(): void
    {
        $cmyk = new \Com\Tecnick\Color\Model\Cmyk([]);
        $this->assertSame(
            [
                'C' => 0.0,
                'M' => 0.0,
                'Y' => 0.0,
                'K' => 0.0,
                'A' => 1.0,
            ],
            $cmyk->getArray(),
        );
    }

    public function testConstructorRejectsUnknownComponents(): void
    {
        $this->bcAssertThrows(
            \Com\Tecnick\Color\UnknownComponentException::class,
            'unknown color components: black',
            static fn() => new \Com\Tecnick\Color\Model\Cmyk(['black' => 0.5]),
        );
    }

    public function testGetRgbaHexColor(): void
    {
        $cmyk = $this->getTestObject();
        $rgbaHexColor = $cmyk->getRgbaHexColor();
        $this->assertSame('#4080bfd9', $rgbaHexColor);
    }

    public function testGetRgbHexColor(): void
    {
        $cmyk = $this->getTestObject();
        $rgbHexColor = $cmyk->getRgbHexColor();
        $this->assertSame('#4080bf', $rgbHexColor);
    }

    public function testGetArray(): void
    {
        $cmyk = $this->getTestObject();
        $res = $cmyk->getArray();
        $this->assertSame(
            [
                'C' => 0.666,
                'M' => 0.333,
                'Y' => 0.0,
                'K' => 0.25,
                'A' => 0.85,
            ],
            $res,
        );
    }

    public function testPDFacArray(): void
    {
        $cmyk = $this->getTestObject();
        $res = $cmyk->getPDFacArray();
        $this->assertSame(
            [
                0.666,
                0.333,
                0.0,
                0.25,
            ],
            $res,
        );
    }

    public function testGetNormalizedArray(): void
    {
        $cmyk = $this->getTestObject();
        $res = $cmyk->getNormalizedArray(100);
        $this->assertSame(
            [
                'C' => 67.0,
                'M' => 33.0,
                'Y' => 0.0,
                'K' => 25.0,
                'A' => 0.85,
            ],
            $res,
        );
    }

    public function testGetCssColor(): void
    {
        $cmyk = $this->getTestObject();
        $cssColor = $cmyk->getCssColor();
        $this->assertSame('cmyka(66.6%,33.3%,0%,25%,0.85)', $cssColor);

        $opaque = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0.666,
            'magenta' => 0.333,
            'yellow' => 0,
            'key' => 0.25,
            'alpha' => 1,
        ]);
        $this->assertSame('cmyk(66.6%,33.3%,0%,25%)', $opaque->getCssColor());
    }

    public function testGetJsPdfColor(): void
    {
        $testObj = $this->getTestObject();
        $res = $testObj->getJsPdfColor();
        $this->assertSame('["CMYK",0.666000,0.333000,0.000000,0.250000]', $res);

        $cmyk = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0.666,
            'magenta' => 0.333,
            'yellow' => 0,
            'key' => 0.25,
            'alpha' => 0,
        ]);
        $res = $cmyk->getJsPdfColor();
        $this->assertSame('["T"]', $res);
    }

    public function testGetComponentsString(): void
    {
        $cmyk = $this->getTestObject();
        $componentsString = $cmyk->getComponentsString();
        $this->assertSame('0.666000 0.333000 0.000000 0.250000', $componentsString);
    }

    public function testGetPdfColor(): void
    {
        $cmyk = $this->getTestObject();
        $res = $cmyk->getPdfColor();
        $this->assertSame('0.666000 0.333000 0.000000 0.250000 k' . "\n", $res);

        $res = $cmyk->getPdfColor(false);
        $this->assertSame('0.666000 0.333000 0.000000 0.250000 k' . "\n", $res);

        $res = $cmyk->getPdfColor(true);
        $this->assertSame('0.666000 0.333000 0.000000 0.250000 K' . "\n", $res);
    }

    public function testToGrayArray(): void
    {
        $cmyk = $this->getTestObject();
        $res = $cmyk->toGrayArray();
        $this->bcAssertEqualsWithDelta([
            'gray' => 0.46518510,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToRgbArray(): void
    {
        $cmyk = $this->getTestObject();
        $res = $cmyk->toRgbArray();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.2505,
            'green' => 0.50025,
            'blue' => 0.75,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToHslArray(): void
    {
        $cmyk = $this->getTestObject();
        $res = $cmyk->toHslArray();
        $this->bcAssertEqualsWithDelta([
            'hue' => 7 / 12,
            'saturation' => 0.49975,
            'lightness' => 0.50025,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToCmykArray(): void
    {
        $cmyk = $this->getTestObject();
        $res = $cmyk->toCmykArray();
        $this->bcAssertEqualsWithDelta([
            'cyan' => 0.666,
            'magenta' => 0.333,
            'yellow' => 0,
            'key' => 0.25,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToLabArray(): void
    {
        $cmyk = $this->getTestObject();
        $res = $cmyk->toLabArray();
        $this->bcAssertEqualsWithDelta([
            'lstar' => 52.041586,
            'astar' => 0.07683,
            'bstar' => -39.325864,
            'alpha' => 0.85,
        ], $res);
    }

    public function testInvertColor(): void
    {
        $cmyk = $this->getTestObject();
        $rgb = $cmyk->toRgbArray();
        $cmyk->invertColor();

        // inversion is the RGB complement of every channel
        $this->bcAssertEqualsWithDelta(
            [
                'red' => 1.0 - ($rgb['red'] ?? 0.0),
                'green' => 1.0 - ($rgb['green'] ?? 0.0),
                'blue' => 1.0 - ($rgb['blue'] ?? 0.0),
                'alpha' => 0.85,
            ],
            $cmyk->toRgbArray(),
            1e-9,
        );

        $this->bcAssertEqualsWithDelta(
            [
                'cyan' => 0.0,
                'magenta' => 0.333222,
                'yellow' => 0.666444,
                'key' => 0.2505,
                'alpha' => 0.85,
            ],
            $cmyk->toCmykArray(),
            1e-6,
        );
    }

    /**
     * The key channel is not independent of C, M and Y: red = (1 - c) * (1 - k),
     * so inversion is computed through RGB.
     */
    public function testInvertColorDoesNotComplementKeyIndependently(): void
    {
        $cmyk = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0,
            'magenta' => 0,
            'yellow' => 0,
            'key' => 0.5,
            'alpha' => 1,
        ]);
        $this->bcAssertEqualsWithDelta(
            [
                'red' => 0.5,
                'green' => 0.5,
                'blue' => 0.5,
                'alpha' => 1,
            ],
            $cmyk->toRgbArray(),
            1e-9,
        );

        $cmyk->invertColor();
        $this->bcAssertEqualsWithDelta(
            [
                'red' => 0.5,
                'green' => 0.5,
                'blue' => 0.5,
                'alpha' => 1,
            ],
            $cmyk->toRgbArray(),
            1e-9,
        );
    }

    /**
     * White and black saturate every RGB channel, the branch where rgbToCmyk()
     * divides by (1 - key).
     */
    public function testInvertColorAtTheEndsOfTheKeyRange(): void
    {
        $white = new \Com\Tecnick\Color\Model\Cmyk([]);
        $white->invertColor();
        $this->assertSame(
            [
                'cyan' => 0.0,
                'magenta' => 0.0,
                'yellow' => 0.0,
                'key' => 1.0,
                'alpha' => 1.0,
            ],
            $white->toCmykArray(),
        );

        $black = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 1,
            'magenta' => 1,
            'yellow' => 1,
            'key' => 1,
        ]);
        $black->invertColor();
        $this->assertSame(
            [
                'cyan' => 0.0,
                'magenta' => 0.0,
                'yellow' => 0.0,
                'key' => 0.0,
                'alpha' => 1.0,
            ],
            $black->toCmykArray(),
        );
    }

    /**
     * Every component of the conversion arrays is a float, including the
     * saturated 0.0 and 1.0 values.
     */
    public function testConversionArraysHoldOnlyFloats(): void
    {
        $saturated = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0,
            'magenta' => 0.5,
            'yellow' => 0,
            'key' => 0,
        ]);
        $this->assertSame(
            [
                'red' => 1.0,
                'green' => 0.5,
                'blue' => 1.0,
                'alpha' => 1.0,
            ],
            $saturated->toRgbArray(),
        );

        $this->assertSame(
            (new \Com\Tecnick\Color\Model\Rgb([
                'red' => 1.0,
                'green' => 0.5,
                'blue' => 1.0,
            ]))->toRgbArray(),
            $saturated->toRgbArray(),
        );

        $this->assertSame(
            [
                'cyan' => 0.0,
                'magenta' => 0.0,
                'yellow' => 0.0,
                'key' => 1.0,
                'alpha' => 1.0,
            ],
            (new \Com\Tecnick\Color\Model\Rgb([
                'red' => 0,
                'green' => 0,
                'blue' => 0,
            ]))->toCmykArray(),
        );
    }
}
