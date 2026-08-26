<?php

declare(strict_types=1);

/**
 * HslTest.php
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
 * Hsl Color class test
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class HslTest extends TestUtil
{
    protected function getTestObject(): \Com\Tecnick\Color\Model\Hsl
    {
        return new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 0.583,
            'saturation' => 0.5,
            'lightness' => 0.5,
            'alpha' => 0.85,
        ]);
    }

    public function testGetType(): void
    {
        $hsl = $this->getTestObject();
        $type = $hsl->getType();
        $this->assertSame('HSL', $type);
    }

    /**
     * Hue is a fraction of a full turn and wraps into [0..1) instead of being
     * clamped.
     */
    public function testConstructorWrapsHue(): void
    {
        $cases = [
            [1.1, 0.1],
            [-0.1, 0.9],
            [2.5, 0.5],
            [1.0, 0.0],
            [0.25, 0.25],
        ];

        foreach ($cases as [$input, $expected]) {
            $hsl = new \Com\Tecnick\Color\Model\Hsl([
                'hue' => $input,
                'saturation' => 1,
                'lightness' => 0.5,
                'alpha' => 1,
            ]);
            $this->bcAssertEqualsWithDelta($expected, $hsl->getArray()['H'] ?? null, 1e-9, 'hue ' . $input);
        }
    }

    public function testConstructorClampsNonAngleComponents(): void
    {
        $hsl = new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 0.5,
            'saturation' => 4,
            'lightness' => -1,
            'alpha' => 9,
        ]);
        $this->assertSame(
            [
                'H' => 0.5,
                'S' => 1.0,
                'L' => 0.0,
                'A' => 1.0,
            ],
            $hsl->getArray(),
        );
    }

    public function testGetRgbaHexColor(): void
    {
        $hsl = $this->getTestObject();
        $rgbaHexColor = $hsl->getRgbaHexColor();
        $this->assertSame('#4080bfd9', $rgbaHexColor);
    }

    public function testGetRgbHexColor(): void
    {
        $hsl = $this->getTestObject();
        $rgbHexColor = $hsl->getRgbHexColor();
        $this->assertSame('#4080bf', $rgbHexColor);
    }

    public function testGetArray(): void
    {
        $hsl = $this->getTestObject();
        $res = $hsl->getArray();
        $this->assertSame(
            [
                'H' => 0.583,
                'S' => 0.5,
                'L' => 0.5,
                'A' => 0.85,
            ],
            $res,
        );
    }

    public function testGetPDFacArray(): void
    {
        $hsl = $this->getTestObject();
        $res = $hsl->getPDFacArray();
        $this->bcAssertEqualsWithDelta(
            [
                0.25,
                0.501,
                0.75,
            ],
            $res,
        );
    }

    public function testGetNormalizedArray(): void
    {
        $hsl = $this->getTestObject();
        $res = $hsl->getNormalizedArray(255);
        $this->assertSame(
            [
                'H' => 210.0,
                'S' => 0.5,
                'L' => 0.5,
                'A' => 0.85,
            ],
            $res,
        );

        // hue is an angle and saturation and lightness are fractions, so $max is ignored
        $this->assertSame($res, $hsl->getNormalizedArray(100));
    }

    public function testGetCssColor(): void
    {
        $hsl = $this->getTestObject();
        $cssColor = $hsl->getCssColor();
        $this->assertSame('hsla(209.88,50%,50%,0.85)', $cssColor);

        $opaque = new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 0.583,
            'saturation' => 0.5,
            'lightness' => 0.5,
            'alpha' => 1,
        ]);
        $this->assertSame('hsl(209.88,50%,50%)', $opaque->getCssColor());
    }

    public function testGetJsPdfColor(): void
    {
        $testObj = $this->getTestObject();
        $res = $testObj->getJsPdfColor();
        $this->assertSame('["RGB",0.250000,0.501000,0.750000]', $res);

        $hsl = new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 0.583,
            'saturation' => 0.5,
            'lightness' => 0.5,
            'alpha' => 0,
        ]);
        $res = $hsl->getJsPdfColor();
        $this->assertSame('["T"]', $res);
    }

    public function testGetComponentsString(): void
    {
        $hsl = $this->getTestObject();
        $componentsString = $hsl->getComponentsString();
        $this->assertSame('0.250000 0.501000 0.750000', $componentsString);
    }

    public function testGetPdfColor(): void
    {
        $hsl = $this->getTestObject();
        $res = $hsl->getPdfColor();
        $this->assertSame('0.250000 0.501000 0.750000 rg' . "\n", $res);

        $res = $hsl->getPdfColor(false);
        $this->assertSame('0.250000 0.501000 0.750000 rg' . "\n", $res);

        $res = $hsl->getPdfColor(true);
        $this->assertSame('0.250000 0.501000 0.750000 RG' . "\n", $res);
    }

    public function testToGrayArray(): void
    {
        $hsl = $this->getTestObject();
        $res = $hsl->toGrayArray();
        $this->bcAssertEqualsWithDelta([
            'gray' => 0.46561520,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToRgbArray(): void
    {
        $testObj = $this->getTestObject();
        $res = $testObj->toRgbArray();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.25,
            'green' => 0.501,
            'blue' => 0.75,
            'alpha' => 0.85,
        ], $res);

        $col = new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 0.583,
            'saturation' => 0.5,
            'lightness' => 0.4,
            'alpha' => 1,
        ]);
        $res = $col->toRgbArray();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.2,
            'green' => 0.4008,
            'blue' => 0.6,
            'alpha' => 1,
        ], $res);

        $col = new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 0.583,
            'saturation' => 0,
            'lightness' => 0.4,
            'alpha' => 1,
        ]);
        $res = $col->toRgbArray();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.400,
            'green' => 0.400,
            'blue' => 0.400,
            'alpha' => 1,
        ], $res);

        $col = new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 0.01,
            'saturation' => 1,
            'lightness' => 0.4,
            'alpha' => 1,
        ]);
        $res = $col->toRgbArray();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.8,
            'green' => 0.048,
            'blue' => 0,
            'alpha' => 1,
        ], $res);

        $col = new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 1,
            'saturation' => 1,
            'lightness' => 0.4,
            'alpha' => 1,
        ]);
        $res = $col->toRgbArray();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.8,
            'green' => 0,
            'blue' => 0,
            'alpha' => 1,
        ], $res);
    }

    public function testToHslArray(): void
    {
        $hsl = $this->getTestObject();
        $res = $hsl->toHslArray();
        $this->bcAssertEqualsWithDelta([
            'hue' => 0.583,
            'saturation' => 0.5,
            'lightness' => 0.5,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToCmykArray(): void
    {
        $hsl = $this->getTestObject();
        $res = $hsl->toCmykArray();
        $this->bcAssertEqualsWithDelta([
            'cyan' => 2 / 3,
            'magenta' => 0.332,
            'yellow' => 0,
            'key' => 0.25,
            'alpha' => 0.85,
        ], $res);
    }

    public function testToLabArray(): void
    {
        $hsl = $this->getTestObject();
        $res = $hsl->toLabArray();
        $this->bcAssertEqualsWithDelta([
            'lstar' => 52.092639,
            'astar' => -0.058712,
            'bstar' => -39.245727,
            'alpha' => 0.85,
        ], $res);
    }

    public function testInvertColor(): void
    {
        $hsl = $this->getTestObject();
        $rgb = $hsl->toRgbArray();
        $hsl->invertColor();

        // inversion is the RGB complement of every channel
        $this->bcAssertEqualsWithDelta(
            [
                'red' => 1.0 - ($rgb['red'] ?? 0.0),
                'green' => 1.0 - ($rgb['green'] ?? 0.0),
                'blue' => 1.0 - ($rgb['blue'] ?? 0.0),
                'alpha' => 0.85,
            ],
            $hsl->toRgbArray(),
            1e-9,
        );

        $res = $hsl->toHslArray();
        $this->bcAssertEqualsWithDelta(
            [
                'hue' => 0.083,
                'saturation' => 0.5,
                'lightness' => 0.5,
                'alpha' => 0.85,
            ],
            $res,
            1e-3,
        );
    }

    /**
     * Inversion complements lightness as well as rotating the hue:
     * hsl(0,100%,25%) is dark red (0.5,0,0) and inverts to light cyan
     * (0.5,1,1) = hsl(180,100%,75%).
     */
    public function testInvertColorComplementsLightness(): void
    {
        $hsl = new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 0.0,
            'saturation' => 1.0,
            'lightness' => 0.25,
            'alpha' => 1,
        ]);
        $hsl->invertColor();

        $this->bcAssertEqualsWithDelta(
            [
                'red' => 0.5,
                'green' => 1.0,
                'blue' => 1.0,
                'alpha' => 1,
            ],
            $hsl->toRgbArray(),
            1e-9,
        );

        $this->bcAssertEqualsWithDelta(
            [
                'hue' => 0.5,
                'saturation' => 1.0,
                'lightness' => 0.75,
                'alpha' => 1,
            ],
            $hsl->toHslArray(),
            1e-9,
        );
    }

    /**
     * Hue values on and around every 60-degree sector boundary, plus the two
     * points that straddle the lightness pivot at 50%.
     *
     * Expectations are computed from the f(n) algorithm of CSS Color Level 4.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function hslBoundaryProvider(): array
    {
        return [
            'sector 0 lower' => ['hsl(359,100%,40%)', '#cc0003'],
            'sector 0' => ['hsl(0,100%,40%)', '#cc0000'],
            'sector 1 lower' => ['hsl(59,100%,40%)', '#ccc900'],
            'sector 1' => ['hsl(60,100%,40%)', '#cccc00'],
            'sector 1 upper' => ['hsl(61,100%,40%)', '#c9cc00'],
            'sector 2 inner' => ['hsl(116,100%,40%)', '#0ecc00'],
            'sector 2 lower' => ['hsl(119,100%,40%)', '#03cc00'],
            'sector 2' => ['hsl(120,100%,40%)', '#00cc00'],
            'sector 2 upper' => ['hsl(121,100%,40%)', '#00cc03'],
            'sector 3 lower' => ['hsl(179,100%,40%)', '#00ccc9'],
            'sector 3' => ['hsl(180,100%,40%)', '#00cccc'],
            'sector 3 upper' => ['hsl(181,100%,40%)', '#00c9cc'],
            'sector 4 inner' => ['hsl(236,100%,40%)', '#000ecc'],
            'sector 4 lower' => ['hsl(239,100%,40%)', '#0003cc'],
            'sector 4' => ['hsl(240,100%,40%)', '#0000cc'],
            'sector 4 upper' => ['hsl(241,100%,40%)', '#0300cc'],
            'sector 4 upper desaturated' => ['hsl(241,10%,40%)', '#5c5c70'],
            'sector 4 wrap' => ['hsl(242,100%,40%)', '#0700cc'],
            'sector 5 lower' => ['hsl(299,100%,40%)', '#c900cc'],
            'sector 5' => ['hsl(300,100%,40%)', '#cc00cc'],
            'sector 5 upper' => ['hsl(301,100%,40%)', '#cc00c9'],
            'pivot below' => ['hsl(210,60%,49.9%)', '#337fcc'],
            'pivot' => ['hsl(210,60%,50%)', '#3380cc'],
            'pivot above' => ['hsl(210,60%,50.1%)', '#3380cc'],
            'pivot upper' => ['hsl(210,60%,50.5%)', '#3581cd'],
        ];
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('hslBoundaryProvider')]
    public function testHueSectorAndLightnessPivotBoundaries(string $color, string $expected): void
    {
        $web = new \Com\Tecnick\Color\Web();
        $obj = $web->getColorObj($color);
        $this->assertNotNull($obj);
        $this->assertSame($expected, $obj->getRgbHexColor(), $color);
    }
}
