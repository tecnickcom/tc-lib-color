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
 * @link        https://github.com/tecnick.com/tc-lib-color
 *
 * This file is part of tc-lib-color software library.
 */

namespace Test;

/**
 * Web Color class test
 *
 * @since       2015-02-21
 * @category    Library
 * @package     Color
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2015-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnick.com/tc-lib-color
 */
class WebTest extends \PHPUnit_Framework_TestCase
{
    protected $obj = null;

    public function setUp()
    {
        //$this->markTestSkipped(); // skip this test
        $this->obj = new \Com\Tecnick\Color\Web;
    }

    public function testGetMap()
    {
        $res = $this->obj->getMap();
        $this->assertEquals(149, count($res));
    }

    public function testGetHexFromName()
    {
        $res = $this->obj->getHexFromName('aliceblue');
        $this->assertEquals('f0f8ffff', $res);
        $res = $this->obj->getHexFromName('color.yellowgreen');
        $this->assertEquals('9acd32ff', $res);
        $this->setExpectedException('\Com\Tecnick\Color\Exception');
        $res = $this->obj->getHexFromName('invalid');
    }

    public function testGetNameFromHex()
    {
        $res = $this->obj->getNameFromHex('f0f8ffff');
        $this->assertEquals('aliceblue', $res);
        $res = $this->obj->getNameFromHex('9acd32ff');
        $this->assertEquals('yellowgreen', $res);
        $this->setExpectedException('\Com\Tecnick\Color\Exception');
        $res = $this->obj->getNameFromHex('012345');
    }

    public function testExtractHexCode()
    {
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
        $this->setExpectedException('\Com\Tecnick\Color\Exception');
        $res = $this->obj->extractHexCode('');
    }

    public function testGetRgbObjFromHex()
    {
        $res = $this->obj->getRgbObjFromHex('#87ceebff');
        $this->assertEquals('#87ceebff', $res->getRgbaHexColor());
        $this->setExpectedException('\Com\Tecnick\Color\Exception');
        $res = $this->obj->getRgbObjFromHex('xx');
    }

    public function testGetRgbObjFromName()
    {
        $res = $this->obj->getRgbObjFromName('skyblue');
        $this->assertEquals('#87ceebff', $res->getRgbaHexColor());
        $this->setExpectedException('\Com\Tecnick\Color\Exception');
        $res = $this->obj->getRgbObjFromName('xx');
    }

    public function testNormalizeValue()
    {
        $res = $this->obj->normalizeValue('50%', 50);
        $this->assertEquals(0.5, $res);
        $res = $this->obj->normalizeValue(128, 255);
        $this->assertEquals(0.5, $res, '', 0.01);
    }

    public function testGetColorObj()
    {
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
        $this->setExpectedException('\Com\Tecnick\Color\Exception');
        $res = $this->obj->getColorObj('g(-)');
        $this->setExpectedException('\Com\Tecnick\Color\Exception');
        $res = $this->obj->getColorObj('rgb(-)');
        $this->setExpectedException('\Com\Tecnick\Color\Exception');
        $res = $this->obj->getColorObj('hsl(-)');
        $this->setExpectedException('\Com\Tecnick\Color\Exception');
        $res = $this->obj->getColorObj('cmyk(-)');
    }
}
