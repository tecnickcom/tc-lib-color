<?php
/**
 * WebTest.php
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

namespace Test;

use PHPUnit\Framework\TestCase;

/**
 * Web Color class test
 *
 * @since       2015-02-21
 * @category    Library
 * @package     Color
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2015-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-color
 */
class WebTest extends TestCase
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
        return new \Com\Tecnick\Color\Web;
    }

    public function testGetMap()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getMap();
        $this->assertEquals(149, count($res));
    }

    public function testGetHexFromName()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getHexFromName('aliceblue');
        $this->assertEquals('f0f8ffff', $res);
        $res = $this->obj->getHexFromName('color.yellowgreen');
        $this->assertEquals('9acd32ff', $res);
    }

    public function testGetHexFromNameInvalid()
    {
        $this->expectException('\Com\Tecnick\Color\Exception');
        $this->obj = $this->getTestObject();
        $this->obj->getHexFromName('invalid');
    }

    public function testGetNameFromHex()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getNameFromHex('f0f8ffff');
        $this->assertEquals('aliceblue', $res);
        $res = $this->obj->getNameFromHex('9acd32ff');
        $this->assertEquals('yellowgreen', $res);
    }

    public function testGetNameFromHexBad()
    {
        $this->expectException('\Com\Tecnick\Color\Exception');
        $this->obj = $this->getTestObject();
        $this->obj->getNameFromHex('012345');
    }

    public function testExtractHexCode()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->extractHexCode('abc');
        $this->assertEquals('aabbccff', $res);
        $res = $this->obj->extractHexCode('#abc');
        $this->assertEquals('aabbccff', $res);
        $res = $this->obj->extractHexCode('abcd');
        $this->assertEquals('aabbccdd', $res);
        $res = $this->obj->extractHexCode('#abcd');
        $this->assertEquals('aabbccdd', $res);
        $res = $this->obj->extractHexCode('112233');
        $this->assertEquals('112233ff', $res);
        $res = $this->obj->extractHexCode('#112233');
        $this->assertEquals('112233ff', $res);
        $res = $this->obj->extractHexCode('11223344');
        $this->assertEquals('11223344', $res);
        $res = $this->obj->extractHexCode('#11223344');
        $this->assertEquals('11223344', $res);
    }

    public function testExtractHexCodeBad()
    {
        $this->expectException('\Com\Tecnick\Color\Exception');
        $this->obj = $this->getTestObject();
        $this->obj->extractHexCode('');
    }

    public function testGetRgbObjFromHex()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getRgbObjFromHex('#87ceebff');
        $this->assertEquals('#87ceebff', $res->getRgbaHexColor());
    }

    public function testGetRgbObjFromHexBad()
    {
        $this->expectException('\Com\Tecnick\Color\Exception');
        $this->obj = $this->getTestObject();
        $this->obj->getRgbObjFromHex('xx');
    }

    public function testGetRgbObjFromName()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getRgbObjFromName('skyblue');
        $this->assertEquals('#87ceebff', $res->getRgbaHexColor());
    }

    public function testGetRgbObjFromNameBad()
    {
        $this->expectException('\Com\Tecnick\Color\Exception');
        $this->obj = $this->getTestObject();
        $this->obj->getRgbObjFromName('xx');
    }

    public function testNormalizeValue()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->normalizeValue('50%', 50);
        $this->assertEquals(0.5, $res);
        $res = $this->obj->normalizeValue(128, 255);
        $this->assertSimilarValues(0.5, $res);
    }

    public function testGetColorObj()
    {
        $this->obj = $this->getTestObject();
        $res = $this->obj->getColorObj('');
        $this->assertNull($res);
        $res = $this->obj->getColorObj('t()');
        $this->assertNull($res);
        $res = $this->obj->getColorObj('["T"]');
        $this->assertNull($res);
        $res = $this->obj->getColorObj('transparent');
        $this->assertNull($res);
        $res = $this->obj->getColorObj('color.transparent');
        $this->assertNull($res);
        $res = $this->obj->getColorObj('royalblue');
        $this->assertEquals('#4169e1ff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('#1a2b3c4d');
        $this->assertEquals('#1a2b3c4d', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('#1a2b3c');
        $this->assertEquals('#1a2b3cff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('#1234');
        $this->assertEquals('#11223344', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('#123');
        $this->assertEquals('#112233ff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('["G",0.5]');
        $this->assertEquals('#808080ff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('["RGB",0.25,0.50,0.75]');
        $this->assertEquals('#4080bfff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('["CMYK",0.666,0.333,0,0.25]');
        $this->assertEquals('#4080bfff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('g(50%)');
        $this->assertEquals('#808080ff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('g(128)');
        $this->assertEquals('#808080ff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('rgb(25%,50%,75%)');
        $this->assertEquals('#4080bfff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('rgb(64,128,191)');
        $this->assertEquals('#4080bfff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('rgba(25%,50%,75%,0.85)');
        $this->assertEquals('#4080bfd9', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('rgba(64,128,191,0.85)');
        $this->assertEquals('#4080bfd9', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('hsl(210,50%,50%)');
        $this->assertEquals('#4080bfff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('hsla(210,50%,50%,0.85)');
        $this->assertEquals('#4080bfd9', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('cmyk(67%,33%,0,25%)');
        $this->assertEquals('#3f80bfff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('cmyk(67,33,0,25)');
        $this->assertEquals('#3f80bfff', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('cmyka(67,33,0,25,0.85)');
        $this->assertEquals('#3f80bfd9', $res->getRgbaHexColor());
        $res = $this->obj->getColorObj('cmyka(67%,33%,0,25%,0.85)');
        $this->assertEquals('#3f80bfd9', $res->getRgbaHexColor());
    }

    public function getBadColor()
    {
        return array(
            array('g(-)'),
            array('rgb(-)'),
            array('hsl(-)'),
            array('cmyk(-)'),
        );
    }
    /**
     * @dataProvider getBadColor
     */
    public function testGetColorObjBad($bad)
    {
        $this->expectException('\Com\Tecnick\Color\Exception');
        $this->obj = $this->getTestObject();
        $this->obj->getColorObj($bad);
    }

    public function testGetRgbSquareDistance()
    {
        $this->obj = $this->getTestObject();
        $cola = array('red' => 0, 'green' => 0, 'blue' => 0);
        $colb = array('red' => 1, 'green' => 1, 'blue' => 1);
        $dist = $this->obj->getRgbSquareDistance($cola, $colb);
        $this->assertEquals(3, $dist);

        $cola = array('red' => 0.5, 'green' => 0.5, 'blue' => 0.5);
        $colb = array('red' => 0.5, 'green' => 0.5, 'blue' => 0.5);
        $dist = $this->obj->getRgbSquareDistance($cola, $colb);
        $this->assertEquals(0, $dist);

        $cola = array('red' => 0.25, 'green' => 0.50, 'blue' => 0.75);
        $colb = array('red' => 0.50, 'green' => 0.75, 'blue' => 1.00);
        $dist = $this->obj->getRgbSquareDistance($cola, $colb);
        $this->assertEquals(0.1875, $dist);
    }

    public function testGetClosestWebColor()
    {
        $this->obj = $this->getTestObject();
        $col = array('red' => 1, 'green' => 0, 'blue' => 0);
        $color = $this->obj->getClosestWebColor($col);
        $this->assertEquals('red', $color);

        $col = array('red' => 0, 'green' => 1, 'blue' => 0);
        $color = $this->obj->getClosestWebColor($col);
        $this->assertEquals('lime', $color);

        $col = array('red' => 0, 'green' => 0, 'blue' => 1);
        $color = $this->obj->getClosestWebColor($col);
        $this->assertEquals('blue', $color);

        $col = array('red' => 0.33, 'green' => 0.4, 'blue' => 0.18);
        $color = $this->obj->getClosestWebColor($col);
        $this->assertEquals('darkolivegreen', $color);
    }
}
