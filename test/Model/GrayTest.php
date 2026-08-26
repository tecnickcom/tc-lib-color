<?php

declare(strict_types=1);

/**
 * GrayTest.php
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
 * Gray Color class test
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class GrayTest extends TestUtil
{
    protected function getTestObject(): \Com\Tecnick\Color\Model\Gray
    {
        return new \Com\Tecnick\Color\Model\Gray([
            'gray' => 0.75,
            'alpha' => 0.85,
        ]);
    }

    public function testGetType(): void
    {
        $gray = $this->getTestObject();
        $type = $gray->getType();
        $this->assertSame('GRAY', $type);
    }

    /**
     * Components are clamped into [0..1] on construction.
     */
    public function testConstructorClampsComponents(): void
    {
        $over = new \Com\Tecnick\Color\Model\Gray([
            'gray' => 2,
            'alpha' => 5,
        ]);
        $this->assertSame(
            [
                'G' => 1.0,
                'A' => 1.0,
            ],
            $over->getArray(),
        );

        $under = new \Com\Tecnick\Color\Model\Gray([
            'gray' => -0.5,
            'alpha' => -2,
        ]);
        $this->assertSame(
            [
                'G' => 0.0,
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
        $gray = new \Com\Tecnick\Color\Model\Gray([]);
        $this->assertSame(
            [
                'G' => 0.0,
                'A' => 1.0,
            ],
            $gray->getArray(),
        );
    }

    public function testConstructorRejectsUnknownComponents(): void
    {
        $this->bcAssertThrows(
            \Com\Tecnick\Color\UnknownComponentException::class,
            'unknown color components: grey',
            static fn() => new \Com\Tecnick\Color\Model\Gray(['grey' => 0.5]),
        );
    }

    public function testGetRgbaHexColor(): void
    {
        $gray = $this->getTestObject();
        $rgbaHexColor = $gray->getRgbaHexColor();
        $this->assertSame('#bfbfbfd9', $rgbaHexColor);
    }

    public function testGetRgbHexColor(): void
    {
        $gray = $this->getTestObject();
        $rgbHexColor = $gray->getRgbHexColor();
        $this->assertSame('#bfbfbf', $rgbHexColor);
    }

    public function testGetArray(): void
    {
        $gray = $this->getTestObject();
        $res = $gray->getArray();
        $this->assertSame(
            [
                'G' => 0.75,
                'A' => 0.85,
            ],
            $res,
        );
    }

    public function testGetPDFacArray(): void
    {
        $gray = $this->getTestObject();
        $res = $gray->getPDFacArray();
        $this->assertSame(
            [
                0.75,
            ],
            $res,
        );
    }

    public function testGetNormalizedArray(): void
    {
        $gray = $this->getTestObject();
        $res = $gray->getNormalizedArray(255);
        $this->assertSame(
            [
                'G' => 191.0,
                'A' => 0.85,
            ],
            $res,
        );
    }

    public function testGetCssColor(): void
    {
        $gray = $this->getTestObject();
        $cssColor = $gray->getCssColor();
        $this->assertSame('rgba(191,191,191,0.85)', $cssColor);

        $opaque = new \Com\Tecnick\Color\Model\Gray([
            'gray' => 0.75,
            'alpha' => 1,
        ]);
        $this->assertSame('g(191)', $opaque->getCssColor());
    }

    public function testGetJsPdfColor(): void
    {
        $testObj = $this->getTestObject();
        $res = $testObj->getJsPdfColor();
        $this->assertSame('["G",0.750000]', $res);

        $gray = new \Com\Tecnick\Color\Model\Gray([
            'gray' => 0.5,
            'alpha' => 0,
        ]);
        $res = $gray->getJsPdfColor();
        $this->assertSame('["T"]', $res);
    }

    public function testGetComponentsString(): void
    {
        $gray = $this->getTestObject();
        $componentsString = $gray->getComponentsString();
        $this->assertSame('0.750000', $componentsString);
    }

    public function testGetPdfColor(): void
    {
        $gray = $this->getTestObject();
        $res = $gray->getPdfColor();
        $this->assertSame('0.750000 g' . "\n", $res);

        $res = $gray->getPdfColor(false);
        $this->assertSame('0.750000 g' . "\n", $res);

        $res = $gray->getPdfColor(true);
        $this->assertSame('0.750000 G' . "\n", $res);
    }

    public function testToGrayArray(): void
    {
        $gray = $this->getTestObject();
        $res = $gray->toGrayArray();
        $this->assertSame(
            [
                'gray' => 0.75,
                'alpha' => 0.85,
            ],
            $res,
        );
    }

    public function testToRgbArray(): void
    {
        $gray = $this->getTestObject();
        $res = $gray->toRgbArray();
        $this->assertSame(
            [
                'red' => 0.75,
                'green' => 0.75,
                'blue' => 0.75,
                'alpha' => 0.85,
            ],
            $res,
        );
    }

    public function testToHslArray(): void
    {
        $gray = $this->getTestObject();
        $res = $gray->toHslArray();
        $this->assertSame(
            [
                'hue' => 0.0,
                'saturation' => 0.0,
                'lightness' => 0.75,
                'alpha' => 0.85,
            ],
            $res,
        );
    }

    public function testToCmykArray(): void
    {
        $gray = $this->getTestObject();
        $res = $gray->toCmykArray();
        $this->assertSame(
            [
                'cyan' => 0.0,
                'magenta' => 0.0,
                'yellow' => 0.0,
                'key' => 0.25,
                'alpha' => 0.85,
            ],
            $res,
        );
    }

    public function testToLabArray(): void
    {
        $gray = $this->getTestObject();
        $res = $gray->toLabArray();
        $this->bcAssertEqualsWithDelta([
            'lstar' => 77.431371890244847,
            'astar' => 0.0,
            'bstar' => 0.0,
            'alpha' => 0.85,
        ], $res);
        // a neutral gray carries no chroma at all
        $this->assertSame(0.0, $res['astar'] ?? 1.0);
        $this->assertSame(0.0, $res['bstar'] ?? 1.0);
    }

    public function testInvertColor(): void
    {
        $gray = $this->getTestObject();
        $gray->invertColor();

        $res = $gray->toGrayArray();
        $this->assertSame(
            [
                'gray' => 0.25,
                'alpha' => 0.85,
            ],
            $res,
        );
    }
}
