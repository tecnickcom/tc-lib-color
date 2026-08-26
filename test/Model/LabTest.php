<?php

declare(strict_types=1);

/**
 * LabTest.php
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
 * Lab Color class test
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class LabTest extends TestUtil
{
    protected function getTestObject(): \Com\Tecnick\Color\Model\Lab
    {
        return new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 52,
            'astar' => 0,
            'bstar' => -39,
            'alpha' => 0.85,
        ]);
    }

    public function testGetType(): void
    {
        $lab = $this->getTestObject();
        $this->assertSame('LAB', $lab->getType());
    }

    /**
     * L* is clamped to [0..100], a* and b* to [-128..127].
     */
    public function testConstructorClampsComponents(): void
    {
        $over = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 200,
            'astar' => 999,
            'bstar' => 999,
            'alpha' => 5,
        ]);
        $this->assertSame(
            [
                'L' => 100.0,
                'a' => 127.0,
                'b' => 127.0,
                'A' => 1.0,
            ],
            $over->getArray(),
        );

        $under = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => -50,
            'astar' => -999,
            'bstar' => -999,
            'alpha' => -1,
        ]);
        $this->assertSame(
            [
                'L' => 0.0,
                'a' => -128.0,
                'b' => -128.0,
                'A' => 0.0,
            ],
            $under->getArray(),
        );
    }

    public function testConstructorDefaults(): void
    {
        $lab = new \Com\Tecnick\Color\Model\Lab([]);
        $this->assertSame(
            [
                'L' => 0.0,
                'a' => 0.0,
                'b' => 0.0,
                'A' => 1.0,
            ],
            $lab->getArray(),
        );
    }

    public function testGetArray(): void
    {
        $lab = $this->getTestObject();
        $this->assertSame(
            [
                'L' => 52.0,
                'a' => 0.0,
                'b' => -39.0,
                'A' => 0.85,
            ],
            $lab->getArray(),
        );
    }

    public function testGetPDFacArray(): void
    {
        $lab = $this->getTestObject();
        $this->bcAssertEqualsWithDelta(
            [
                0.252784,
                0.499848,
                0.747328,
            ],
            $lab->getPDFacArray(),
        );
    }

    public function testGetNormalizedArray(): void
    {
        $lab = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 51.6,
            'astar' => 0.4,
            'bstar' => -38.7,
            'alpha' => 0.85,
        ]);

        $this->assertSame(
            [
                'L' => 52.0,
                'a' => 0.0,
                'b' => -39.0,
                'A' => 0.85,
            ],
            $lab->getNormalizedArray(255),
        );

        // L*, a* and b* have ranges of their own, so $max is ignored
        $this->assertSame($lab->getNormalizedArray(255), $lab->getNormalizedArray(100));
    }

    public function testGetCssColor(): void
    {
        $lab = $this->getTestObject();
        $this->assertSame('lab(52% 0 -39 / 0.85)', $lab->getCssColor());

        $opaque = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 52,
            'astar' => 0,
            'bstar' => -39,
            'alpha' => 1,
        ]);
        $this->assertSame('lab(52% 0 -39)', $opaque->getCssColor());
    }

    /**
     * Components are emitted in fixed notation, never in scientific notation.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetCssColorUsesFixedNotation(): void
    {
        $lab = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 0.000_01,
            'astar' => -0.000_01,
            'bstar' => 0.000_01,
            'alpha' => 0.000_01,
        ]);
        $this->assertSame('lab(0% 0 0 / 0)', $lab->getCssColor());

        $rounded = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 69.237_798_446_837,
            'astar' => -12.345_678,
            'bstar' => 4.898_723_715_562_9,
        ]);
        $this->assertSame('lab(69.2378% -12.3457 4.8987)', $rounded->getCssColor());

        $web = new \Com\Tecnick\Color\Web();
        $parsed = $web->getColorObj($rounded->getCssColor());
        $this->assertNotNull($parsed);
        $this->bcAssertEqualsWithDelta($rounded->toRgbArray(), $parsed->toRgbArray(), 1e-4);
    }

    /**
     * A neutral color has no chroma: a* and b* are zero.
     */
    public function testNeutralColorsHaveNoChroma(): void
    {
        foreach ([0.0, 0.25, 0.5, 0.75, 1.0] as $level) {
            $lab = (new \Com\Tecnick\Color\Model\Rgb([
                'red' => $level,
                'green' => $level,
                'blue' => $level,
            ]))->toLabArray();
            $this->bcAssertEqualsWithDelta(0.0, $lab['astar'] ?? 1.0, 1e-12, 'a* at level ' . $level);
            $this->bcAssertEqualsWithDelta(0.0, $lab['bstar'] ?? 1.0, 1e-12, 'b* at level ' . $level);
        }

        $white = (new \Com\Tecnick\Color\Model\Rgb([
            'red' => 1.0,
            'green' => 1.0,
            'blue' => 1.0,
        ]))->toLabArray();
        $this->assertSame(100.0, $white['lstar'] ?? 0.0);
        $this->assertSame(0.0, $white['astar'] ?? 1.0);
        $this->assertSame(0.0, $white['bstar'] ?? 1.0);
    }

    public function testGetJsPdfColor(): void
    {
        $lab = $this->getTestObject();
        $this->bcAssertSameJsColor('["RGB",0.252784,0.499848,0.747328]', $lab->getJsPdfColor());
    }

    public function testGetJsPdfTransparentColor(): void
    {
        $transparent = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 52,
            'astar' => 0,
            'bstar' => -39,
            'alpha' => 0,
        ]);

        $this->assertSame('["T"]', $transparent->getJsPdfColor());
    }

    public function testGetComponentsString(): void
    {
        $lab = $this->getTestObject();
        $this->assertSame('52.000000 0.000000 -39.000000', $lab->getComponentsString());
    }

    public function testGetPdfColor(): void
    {
        $lab = $this->getTestObject();
        // Lab goes through pow(), whose last digits depend on the platform libm
        $this->bcAssertSamePdfOperator('0.252784 0.499848 0.747328 rg' . "\n", $lab->getPdfColor(), 1e-5);
        $this->bcAssertSamePdfOperator('0.252784 0.499848 0.747328 RG' . "\n", $lab->getPdfColor(true), 1e-5);
    }

    public function testToGrayArray(): void
    {
        $lab = $this->getTestObject();
        $this->bcAssertEqualsWithDelta([
            'gray' => 0.46519,
            'alpha' => 0.85,
        ], $lab->toGrayArray());
    }

    public function testToRgbArray(): void
    {
        $lab = $this->getTestObject();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.252784,
            'green' => 0.499848,
            'blue' => 0.747328,
            'alpha' => 0.85,
        ], $lab->toRgbArray());
    }

    /**
     * The Lab gamut is wider than sRGB, so the corners of the a* and b* range
     * drive every channel out of [0..1]. toRgbArray() and getPDFacArray() clamp
     * them back.
     */
    public function testToRgbArrayClampsOutOfGamutColors(): void
    {
        $cyan = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 100,
            'astar' => -128,
            'bstar' => -128,
            'alpha' => 1,
        ]);
        $this->assertSame(
            [
                'red' => 0.0,
                'green' => 1.0,
                'blue' => 1.0,
                'alpha' => 1.0,
            ],
            $cyan->toRgbArray(),
        );
        $this->assertSame([0.0, 1.0, 1.0], $cyan->getPDFacArray());

        // only the extremes of each channel are pinned; the third channel of
        // these two stays inside the gamut and is not clamped
        $orange = (new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 100,
            'astar' => 127,
            'bstar' => 127,
            'alpha' => 1,
        ]))->toRgbArray();
        $this->assertSame(1.0, $orange['red'] ?? -1.0);
        $this->assertSame(0.0, $orange['blue'] ?? -1.0);
        $this->bcAssertEqualsWithDelta(0.275190, $orange['green'] ?? -1.0);

        $dark = (new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 0,
            'astar' => -128,
            'bstar' => 127,
            'alpha' => 1,
        ]))->toRgbArray();
        $this->assertSame(0.0, $dark['red'] ?? -1.0);
        $this->assertSame(0.0, $dark['blue'] ?? -1.0);
        $this->bcAssertEqualsWithDelta(0.177772, $dark['green'] ?? -1.0);
    }

    public function testToLabArray(): void
    {
        $lab = $this->getTestObject();
        $this->bcAssertEqualsWithDelta([
            'lstar' => 52,
            'astar' => 0,
            'bstar' => -39,
            'alpha' => 0.85,
        ], $lab->toLabArray());
    }

    public function testToCmykArray(): void
    {
        $lab = $this->getTestObject();
        $this->bcAssertEqualsWithDelta([
            'cyan' => 0.661749,
            'magenta' => 0.331153,
            'yellow' => 0,
            'key' => 0.252672,
            'alpha' => 0.85,
        ], $lab->toCmykArray());
    }

    public function testToHslArray(): void
    {
        $lab = $this->getTestObject();
        $this->bcAssertEqualsWithDelta([
            'hue' => 0.583404,
            'saturation' => 0.494599,
            'lightness' => 0.500056,
            'alpha' => 0.85,
        ], $lab->toHslArray());
    }

    /**
     * Pins the constants of the inverse pivot and of the linear-to-sRGB segment.
     */
    public function testToRgbArrayLowLightnessBranch(): void
    {
        $lab = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => 2,
            'astar' => 0,
            'bstar' => 0,
            'alpha' => 1,
        ]);

        $rgb = $lab->toRgbArray();
        $this->bcAssertEqualsWithDelta(0.028606519, $rgb['red'] ?? 0.0, 1e-9);
        // the channels agree to well under one 8-bit level, but are not bit-identical:
        // the matrix rows are normalized on the white point, not on this gray
        $this->bcAssertEqualsWithDelta($rgb['red'] ?? 0.0, $rgb['green'] ?? 0.0, 1e-6);
        $this->bcAssertEqualsWithDelta($rgb['green'] ?? 0.0, $rgb['blue'] ?? 0.0, 1e-6);
        $this->assertSame(1.0, $rgb['alpha'] ?? 0.0);
    }

    /**
     * L* = 8 is where the inverse conversion switches from the linear segment
     * to the cubic. Values on either side pin the threshold.
     */
    public function testToRgbArrayStraddlesTheLightnessThreshold(): void
    {
        foreach ([
            [7.9, 0.091443115],
            [8.1, 0.092979405],
            // straddles the linear-to-sRGB threshold of 0.0031308
            [2.84, 0.040618145],
            [2.70, 0.038618801],
        ] as [$lstar, $red]) {
            $lab = new \Com\Tecnick\Color\Model\Lab([
                'lstar' => $lstar,
                'astar' => 0,
                'bstar' => 0,
                'alpha' => 1,
            ]);
            $this->bcAssertEqualsWithDelta($red, $lab->toRgbArray()['red'] ?? 0.0, 1e-9);
        }
    }

    public function testInvertColor(): void
    {
        $lab = $this->getTestObject();
        $lab->invertColor();
        $this->bcAssertEqualsWithDelta([
            'red' => 0.747216,
            'green' => 0.500152,
            'blue' => 0.252672,
            'alpha' => 0.85,
        ], $lab->toRgbArray());
    }
}
