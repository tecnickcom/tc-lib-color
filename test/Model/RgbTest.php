<?php

declare(strict_types=1);

/**
 * RgbTest.php
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
 * Rgb Color class test
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class RgbTest extends TestUtil
{
    protected function getTestObject(): \Com\Tecnick\Color\Model\Rgb
    {
        return new \Com\Tecnick\Color\Model\Rgb([
            'red' => 0.25,
            'green' => 0.50,
            'blue' => 0.75,
            'alpha' => 0.85,
        ]);
    }

    public function testGetType(): void
    {
        $rgb = $this->getTestObject();
        $type = $rgb->getType();
        $this->assertSame('RGB', $type);
    }

    /**
     * Components are clamped into [0..1] on construction.
     */
    public function testConstructorClampsComponents(): void
    {
        $over = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 2,
            'green' => -1,
            'blue' => 0.5,
            'alpha' => 5,
        ]);
        $this->assertSame(
            [
                'R' => 1.0,
                'G' => 0.0,
                'B' => 0.5,
                'A' => 1.0,
            ],
            $over->getArray(),
        );

        $under = new \Com\Tecnick\Color\Model\Rgb([
            'red' => -0.5,
            'green' => 1.5,
            'blue' => 1,
            'alpha' => -2,
        ]);
        $this->assertSame(
            [
                'R' => 0.0,
                'G' => 1.0,
                'B' => 1.0,
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
        $rgb = new \Com\Tecnick\Color\Model\Rgb([]);
        $this->assertSame(
            [
                'R' => 0.0,
                'G' => 0.0,
                'B' => 0.0,
                'A' => 1.0,
            ],
            $rgb->getArray(),
        );
    }

    /**
     * Numeric strings are accepted, as passed by the CSS parser.
     */
    public function testConstructorAcceptsNumericStrings(): void
    {
        $rgb = new \Com\Tecnick\Color\Model\Rgb([
            'red' => '0.25',
            'green' => '0.5',
            'blue' => '0.75',
            'alpha' => '1',
        ]);
        $this->assertSame(
            [
                'R' => 0.25,
                'G' => 0.5,
                'B' => 0.75,
                'A' => 1.0,
            ],
            $rgb->getArray(),
        );
    }

    public function testGetRgbaHexColor(): void
    {
        $rgb = $this->getTestObject();
        $rgbaHexColor = $rgb->getRgbaHexColor();
        $this->assertSame('#4080bfd9', $rgbaHexColor);
    }

    public function testGetRgbHexColor(): void
    {
        $rgb = $this->getTestObject();
        $rgbHexColor = $rgb->getRgbHexColor();
        $this->assertSame('#4080bf', $rgbHexColor);
    }

    public function testGetArray(): void
    {
        $rgb = $this->getTestObject();
        $res = $rgb->getArray();
        $this->assertSame(
            [
                'R' => 0.25,
                'G' => 0.50,
                'B' => 0.75,
                'A' => 0.85,
            ],
            $res,
        );
    }

    public function testGetPDFacArray(): void
    {
        $rgb = $this->getTestObject();
        $res = $rgb->getPDFacArray();
        $this->assertSame(
            [
                0.25,
                0.50,
                0.75,
            ],
            $res,
        );
    }

    public function testGetNormalizedArray(): void
    {
        $rgb = $this->getTestObject();
        $res = $rgb->getNormalizedArray(255);
        $this->assertSame(
            [
                'R' => 64.0,
                'G' => 128.0,
                'B' => 191.0,
                'A' => 0.85,
            ],
            $res,
        );
    }

    public function testGetCssColor(): void
    {
        $rgb = $this->getTestObject();
        $cssColor = $rgb->getCssColor();
        $this->assertSame('rgba(64,128,191,0.85)', $cssColor);

        $opaque = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 0.25,
            'green' => 0.50,
            'blue' => 0.75,
            'alpha' => 1,
        ]);
        $this->assertSame('rgb(64,128,191)', $opaque->getCssColor());
    }

    public function testGetJsPdfColor(): void
    {
        $testObj = $this->getTestObject();
        $res = $testObj->getJsPdfColor();
        $this->assertSame('["RGB",0.250000,0.500000,0.750000]', $res);

        $rgb = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 0.25,
            'green' => 0.50,
            'blue' => 0.75,
            'alpha' => 0,
        ]);
        $res = $rgb->getJsPdfColor();
        $this->assertSame('["T"]', $res);
    }

    public function testGetComponentsString(): void
    {
        $rgb = $this->getTestObject();
        $componentsString = $rgb->getComponentsString();
        $this->assertSame('0.250000 0.500000 0.750000', $componentsString);
    }

    public function testGetPdfColor(): void
    {
        $rgb = $this->getTestObject();
        $res = $rgb->getPdfColor();
        $this->assertSame('0.250000 0.500000 0.750000 rg' . "\n", $res);

        $res = $rgb->getPdfColor(false);
        $this->assertSame('0.250000 0.500000 0.750000 rg' . "\n", $res);

        $res = $rgb->getPdfColor(true);
        $this->assertSame('0.250000 0.500000 0.750000 RG' . "\n", $res);
    }

    public function testToGrayArray(): void
    {
        $rgb = $this->getTestObject();
        $res = $rgb->toGrayArray();
        $this->bcAssertEqualsWithDelta([
            'gray' => 0.4649,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToRgbArray(): void
    {
        $rgb = $this->getTestObject();
        $res = $rgb->toRgbArray();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.25,
            'green' => 0.50,
            'blue' => 0.75,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToHslArray(): void
    {
        $testObj = $this->getTestObject();
        $res = $testObj->toHslArray();
        $this->bcAssertEqualsWithDelta([
            'hue' => 7 / 12,
            'saturation' => 0.5,
            'lightness' => 0.5,
            'alpha' => 0.85,
        ], $res);

        $col = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 0,
            'green' => 0,
            'blue' => 0,
            'alpha' => 1,
        ]);
        $res = $col->toHslArray();
        $this->bcAssertEqualsWithDelta([
            'hue' => 0,
            'saturation' => 0,
            'lightness' => 0,
            'alpha' => 1,
        ], $res);

        $col = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 0.1,
            'green' => 0.3,
            'blue' => 0.2,
            'alpha' => 1,
        ]);
        $res = $col->toHslArray();
        $this->bcAssertEqualsWithDelta([
            'hue' => 5 / 12,
            'saturation' => 0.5,
            'lightness' => 0.2,
            'alpha' => 1,
        ], $res);

        // red is the maximum and green equals blue, so the hue sits at the start
        // of the first sector and comes back as 0, the range being [0..1)
        foreach ([[1.0, 0.2, 0.2], [1.0, 0.0, 0.0], [0.5, 0.0, 0.0]] as [$red, $green, $blue]) {
            $col = new \Com\Tecnick\Color\Model\Rgb([
                'red' => $red,
                'green' => $green,
                'blue' => $blue,
                'alpha' => 1,
            ]);
            $this->assertSame(0.0, $col->toHslArray()['hue'] ?? -1.0);
        }

        $col = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 0.3,
            'green' => 0.2,
            'blue' => 0.1,
            'alpha' => 1,
        ]);
        $res = $col->toHslArray();
        $this->bcAssertEqualsWithDelta([
            'hue' => 1 / 12,
            'saturation' => 0.5,
            'lightness' => 0.2,
            'alpha' => 1,
        ], $res);

        $col = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 1,
            'green' => 0.1,
            'blue' => 0.9,
            'alpha' => 1,
        ]);
        $res = $col->toHslArray();
        $this->bcAssertEqualsWithDelta([
            'hue' => 23 / 27,
            'saturation' => 1,
            'lightness' => 0.55,
            'alpha' => 1,
        ], $res);
    }

    public function testToCmykArray(): void
    {
        $testObj = $this->getTestObject();
        $res = $testObj->toCmykArray();
        $this->bcAssertEqualsWithDelta([
            'cyan' => 2 / 3,
            'magenta' => 1 / 3,
            'yellow' => 0,
            'key' => 0.25,
            'alpha' => 0.85,
        ], $res);

        $rgb = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 0,
            'green' => 0,
            'blue' => 0,
            'alpha' => 1,
        ]);
        $res = $rgb->toCmykArray();
        $this->bcAssertEqualsWithDelta([
            'cyan' => 0,
            'magenta' => 0,
            'yellow' => 0,
            'key' => 1,
            'alpha' => 1,
        ], $res);
    }

    public function testToLabArray(): void
    {
        $rgb = $this->getTestObject();
        $res = $rgb->toLabArray();
        $this->bcAssertEqualsWithDelta([
            'lstar' => 52.018185,
            'astar' => 0.093408,
            'bstar' => -39.36307,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToLabArrayLowValueBranch(): void
    {
        $rgb = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 0,
            'green' => 0,
            'blue' => 0,
            'alpha' => 1,
        ]);

        $lab = $rgb->toLabArray();
        $this->assertSame(0.0, $lab['lstar'] ?? 0.0);
        $this->assertSame(0.0, $lab['astar'] ?? 1.0);
        $this->assertSame(0.0, $lab['bstar'] ?? 1.0);
        $this->assertSame(1.0, $lab['alpha'] ?? 0.0);
    }

    /**
     * These grays land inside the linear segment of the sRGB transfer function
     * and pin its slope.
     */
    public function testToLabArrayLinearSegmentValues(): void
    {
        foreach ([
            [1, 0.274174800],
            [5, 1.370874000],
            [10, 2.741748001],
        ] as [$level, $lstar]) {
            $gray = new \Com\Tecnick\Color\Model\Rgb([
                'red' => $level / 255,
                'green' => $level / 255,
                'blue' => $level / 255,
                'alpha' => 1,
            ]);
            $this->bcAssertEqualsWithDelta($lstar, $gray->toLabArray()['lstar'] ?? 0.0, 1e-9);
        }
    }

    /**
     * The sRGB transfer function switches from the linear segment to the power
     * curve at 0.04045. Values on either side pin the threshold.
     */
    public function testToLabArrayStraddlesTheSrgbThreshold(): void
    {
        foreach ([
            [0.0404, 2.824548790],
            [0.0405, 2.831603355],
            // straddles the CIE epsilon of the XYZ to Lab pivot (L* = 8)
            [0.0925, 8.037518848],
            [0.0900, 7.714495531],
        ] as [$component, $lstar]) {
            $gray = new \Com\Tecnick\Color\Model\Rgb([
                'red' => $component,
                'green' => $component,
                'blue' => $component,
                'alpha' => 1,
            ]);
            $this->bcAssertEqualsWithDelta($lstar, $gray->toLabArray()['lstar'] ?? 0.0, 1e-9);
        }
    }

    public function testInvertColor(): void
    {
        $rgb = $this->getTestObject();
        $rgb->invertColor();

        $res = $rgb->toRgbArray();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.75,
            'green' => 0.50,
            'blue' => 0.25,
            'alpha' => 0.85,
        ], $res);
    }
}
