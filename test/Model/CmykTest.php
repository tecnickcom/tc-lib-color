<?php
/**
 * CmykTest.php
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
 * Cmyk Color class test
 *
 * @since       2015-02-21
 * @category    Library
 * @package     Color
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2015-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-color
 */
class CmykTest extends TestCase
{
    public function bcAssertEqualsWithDelta($expected, $actual, $delta = 0.01, $message = '')
    {
        if (\is_callable(['parent', 'assertEqualsWithDelta'])) {
            return parent::assertEqualsWithDelta($expected, $actual, $delta, $message);
        }
        return $this->assertEquals($expected, $actual, $message, $delta);
    }

    protected function getTestObject()
    {
        return new \Com\Tecnick\Color\Model\Cmyk(
            array(
                'cyan'    => 0.666,
                'magenta' => 0.333,
                'yellow'  => 0,
                'key'     => 0.25,
                'alpha'   => 0.85
            )
        );
    }

    public function testGetType()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getType();
        $this->assertEquals('CMYK', $res);
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
                'C' => 0.666,
                'M' => 0.333,
                'Y' => 0,
                'K' => 0.25,
                'A' => 0.85
            ),
            $res
        );
    }

    public function testGetNormalizedArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getNormalizedArray(100);
        $this->assertEquals(
            array(
                'C' => 67,
                'M' => 33,
                'Y' => 0,
                'K' => 25,
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
        $this->assertEquals('["CMYK",0.666000,0.333000,0.000000,0.250000]', $res);

        $col = new \Com\Tecnick\Color\Model\Cmyk(
            array(
                'cyan'    => 0.666,
                'magenta' => 0.333,
                'yellow'  => 0,
                'key'     => 0.25,
                'alpha'   => 0
            )
        );
        $res = $col->getJsPdfColor();
        $this->assertEquals('["T"]', $res);
    }

    public function testGetComponentsString()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getComponentsString();
        $this->assertEquals('0.666000 0.333000 0.000000 0.250000', $res);
    }

    public function testGetPdfColor()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getPdfColor();
        $this->assertEquals('0.666000 0.333000 0.000000 0.250000 k'."\n", $res);
        
        $res = $this->obj->getPdfColor(false);
        $this->assertEquals('0.666000 0.333000 0.000000 0.250000 k'."\n", $res);
        
        $res = $this->obj->getPdfColor(true);
        $this->assertEquals('0.666000 0.333000 0.000000 0.250000 K'."\n", $res);
    }

    public function testToGrayArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->toGrayArray();
        $this->bcAssertEqualsWithDelta(
            array(
                'gray'  => 0.25,
                'alpha' => 0.85
            ),
            $res
        );
    }

    public function testToRgbArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->toRgbArray();
        $this->bcAssertEqualsWithDelta(
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
        $this->bcAssertEqualsWithDelta(
            array(
                'hue'        => 0.583,
                'saturation' => 0.5,
                'lightness'  => 0.5,
                'alpha'      => 0.85
            ),
            $res
        );
    }

    public function testToCmykArray()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->toCmykArray();
        $this->bcAssertEqualsWithDelta(
            array(
                'cyan'    => 0.666,
                'magenta' => 0.333,
                'yellow'  => 0,
                'key'     => 0.25,
                'alpha'   => 0.85
            ),
            $res
        );
    }

    public function testInvertColor()
    {
        $this->obj = $this->getTestObject();
        $this->obj->invertColor();
        $res = $this->obj->toCmykArray();
        $this->bcAssertEqualsWithDelta(
            array(
                'cyan'    => 0.333,
                'magenta' => 0.666,
                'yellow'  => 1,
                'key'     => 0.75,
                'alpha'   => 0.85
            ),
            $res
        );
    }
}
