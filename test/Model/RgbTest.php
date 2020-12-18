<?php
/**
 * RgbTest.php
 *
 * @since       2015-02-21
 * @category    Library
 * @package     Color
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2015-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-color
 *
 * This file is part of tc-lib-color software library.
 */

namespace Test\Model;

use PHPUnit\Framework\TestCase;

/**
 * Rgb Color class test
 *
 * @since       2015-02-21
 * @category    Library
 * @package     Color
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2015-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-color
 */
class RgbTest extends TestCase
{
    public function assertSimilarValues($expected, $actual, $delta = 0.01, $message = '')
    {
        if (\is_callable(['parent', 'assertEqualsWithDelta'])) {
            return parent::assertEqualsWithDelta($expected, $actual, $delta, $message);
        }
        return $this->assertEquals($expected, $actual, $message, $delta);
    }

    protected function getTestObject()
    {
        return new \Com\Tecnick\Color\Model\Rgb(
            array(
                'red'   => 0.25,
                'green' => 0.50,
                'blue'  => 0.75,
                'alpha' => 0.85
            )
        );
    }

    public function testGetType()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getType();
        $this->assertEquals('RGB', $res);
    }

    public function testGetNormalizedValue()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getNormalizedValue(0.5, 255);
        $this->assertEquals(128, $res);
    }

    public function testGetHexValue()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getHexValue(0.5, 255);
        $this->assertEquals('80', $res);
    }

    public function testGetRgbaHexColor()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getRgbaHexColor();
        $this->assertEquals('#4080bfd9', $res);
    }

    public function testGetRgbHexColor()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getRgbHexColor();
        $this->assertEquals('#4080bf', $res);
    }

    public function testGetArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getArray();
        $this->assertEquals(
            array(
                'R' => 0.25,
                'G' => 0.50,
                'B' => 0.75,
                'A' => 0.85
            ),
            $res
        );
    }

    public function testGetNormalizedArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getNormalizedArray(255);
        $this->assertEquals(
            array(
                'R' => 64,
                'G' => 128,
                'B' => 191,
                'A' => 0.85
            ),
            $res
        );
    }

    public function testGetCssColor()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getCssColor();
        $this->assertEquals('rgba(25%,50%,75%,0.85)', $res);
    }

    public function testGetJsPdfColor()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getJsPdfColor();
        $this->assertEquals('["RGB",0.250000,0.500000,0.750000]', $res);

        $col = new \Com\Tecnick\Color\Model\Rgb(
            array(
                'red'   => 0.25,
                'green' => 0.50,
                'blue'  => 0.75,
                'alpha' => 0
            )
        );
        $res = $col->getJsPdfColor();
        $this->assertEquals('["T"]', $res);
    }

    public function testGetComponentsString()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getComponentsString();
        $this->assertEquals('0.250000 0.500000 0.750000', $res);
    }

    public function testGetPdfColor()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getPdfColor();
        $this->assertEquals('0.250000 0.500000 0.750000 rg'."\n", $res);

        $res = $this->obj->getPdfColor(false);
        $this->assertEquals('0.250000 0.500000 0.750000 rg'."\n", $res);

        $res = $this->obj->getPdfColor(true);
        $this->assertEquals('0.250000 0.500000 0.750000 RG'."\n", $res);
    }

    public function testToGrayArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->toGrayArray();
        $this->assertSimilarValues(
            array(
                'gray'  => 0.465,
                'alpha' => 0.85
            ),
            $res
        );
    }

    public function testToRgbArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->toRgbArray();
        $this->assertSimilarValues(
            array(
                'red'   => 0.25,
                'green' => 0.50,
                'blue'  => 0.75,
                'alpha' => 0.85
            ),
            $res
        );
    }

    public function testToHslArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->toHslArray();
        $this->assertSimilarValues(
            array(
                'hue'        => 0.583,
                'saturation' => 0.5,
                'lightness'  => 0.5,
                'alpha'      => 0.85
            ),
            $res
        );

        $col = new \Com\Tecnick\Color\Model\Rgb(
            array(
                'red'   => 0,
                'green' => 0,
                'blue'  => 0,
                'alpha' => 1
            )
        );
        $res = $col->toHslArray();
        $this->assertSimilarValues(
            array(
                'hue'        => 0,
                'saturation' => 0,
                'lightness'  => 0,
                'alpha'      => 1
            ),
            $res
        );

        $col = new \Com\Tecnick\Color\Model\Rgb(
            array(
                'red'   => 0.1,
                'green' => 0.3,
                'blue'  => 0.2,
                'alpha' => 1
            )
        );
        $res = $col->toHslArray();
        $this->assertSimilarValues(
            array(
                'hue'        => 0.416,
                'saturation' => 0.500,
                'lightness'  => 0.200,
                'alpha'      => 1
            ),
            $res
        );

        $col = new \Com\Tecnick\Color\Model\Rgb(
            array(
                'red'   => 0.3,
                'green' => 0.2,
                'blue'  => 0.1,
                'alpha' => 1
            )
        );
        $res = $col->toHslArray();
        $this->assertSimilarValues(
            array(
                'hue'        => 0.0833,
                'saturation' => 0.500,
                'lightness'  => 0.200,
                'alpha'      => 1
            ),
            $res
        );

        $col = new \Com\Tecnick\Color\Model\Rgb(
            array(
                'red'   => 1,
                'green' => 0.1,
                'blue'  => 0.9,
                'alpha' => 1
            )
        );
        $res = $col->toHslArray();
        $this->assertSimilarValues(
            array(
                'hue'        => 0.852,
                'saturation' => 1,
                'lightness'  => 0.55,
                'alpha'      => 1
            ),
            $res
        );
    }

    public function testToCmykArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->toCmykArray();
        $this->assertSimilarValues(
            array(
                'cyan'    => 0.666,
                'magenta' => 0.333,
                'yellow'  => 0,
                'key'     => 0.25,
                'alpha'   => 0.85
            ),
            $res
        );

        $col = new \Com\Tecnick\Color\Model\Rgb(
            array(
                'red'   => 0,
                'green' => 0,
                'blue'  => 0,
                'alpha' => 1
            )
        );
        $res = $col->toCmykArray();
        $this->assertSimilarValues(
            array(
                'cyan'    => 0,
                'magenta' => 0,
                'yellow'  => 0,
                'key'     => 1,
                'alpha'   => 1
            ),
            $res
        );
    }

    public function testInvertColor()
    {
        $this->obj = $this->getTestObject();
        $this->obj->invertColor();
        $res = $this->obj->toRgbArray();
        $this->assertSimilarValues(
            array(
                'red'   => 0.75,
                'green' => 0.50,
                'blue'  => 0.25,
                'alpha' => 0.85
            ),
            $res
        );
    }
}
