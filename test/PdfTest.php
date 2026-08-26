<?php

declare(strict_types=1);

/**
 * PdfTest.php
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

namespace Test;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Pdf Color class test
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class PdfTest extends TestUtil
{
    protected function getTestObject(): \Com\Tecnick\Color\Pdf
    {
        return new \Com\Tecnick\Color\Pdf();
    }

    /**
     * The shared table projected to (color, expected #RRGGBBAA).
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function parsable(): array
    {
        return \array_map(static fn(array $case): array => [$case[0], $case[1]], ColorStringProvider::parsable());
    }

    /**
     * The shared table projected to (color, expected PDF operator).
     *
     * @return array<string, array{0: string, 1: string, 2: float}>
     */
    public static function pdfOperators(): array
    {
        return \array_map(static fn(array $case): array => [
            $case[0],
            $case[2],
            $case[3],
        ], ColorStringProvider::parsable());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function noColor(): array
    {
        return ColorStringProvider::noColor();
    }

    /**
     * The shared table projected to the color alone.
     *
     * @return array<string, array{0: string}>
     */
    public static function malformed(): array
    {
        return \array_map(static fn(array $case): array => [$case[0]], ColorStringProvider::malformed());
    }

    /**
     * A color that parses yields an Acrobat JavaScript component array carrying
     * the same values as the PDF operator.
     */
    #[DataProvider('parsable')]
    public function testGetJsColorString(string $color, string $expectedHex): void
    {
        $pdf = $this->getTestObject();
        $res = $pdf->getJsColorString($color);

        $model = $pdf->getColorObject($color);
        $this->assertNotNull($model);
        $this->assertSame($expectedHex, $model->getRgbaHexColor());
        $this->assertSame($model->getJsPdfColor(), $res);
        $this->assertStringStartsWith('["', $res);
    }

    /**
     * Names in the JSCOLOR table are emitted as Acrobat color constants.
     */
    public function testGetJsColorStringNamedConstants(): void
    {
        $pdf = $this->getTestObject();
        $this->assertSame('color.transparent', $pdf->getJsColorString('transparent'));
        $this->assertSame('color.magenta', $pdf->getJsColorString('magenta'));
        $this->assertSame('color.dkGray', $pdf->getJsColorString('dkGray'));

        // the match is case-sensitive: a differently-cased name falls through
        // to the parser and yields a component array
        $this->assertSame('["RGB",0.662745,0.662745,0.662745]', $pdf->getJsColorString('dkgray'));
    }

    /**
     * Anything that does not parse falls back to the transparent constant.
     */
    #[DataProvider('malformed')]
    public function testGetJsColorStringMalformed(string $color): void
    {
        $pdf = $this->getTestObject();
        $this->assertSame('color.transparent', $pdf->getJsColorString($color));
    }

    #[DataProvider('noColor')]
    public function testGetJsColorStringNoColor(string $color): void
    {
        $pdf = $this->getTestObject();
        $this->assertSame('color.transparent', $pdf->getJsColorString($color));
    }

    /**
     * Read-only lookup: a parsable color resolves to the same color the parser
     * produces.
     */
    #[DataProvider('parsable')]
    public function testGetColorObject(string $color, string $expectedHex): void
    {
        $pdf = $this->getTestObject();
        $res = $pdf->getColorObject($color);
        $this->assertNotNull($res);
        $this->assertSame($expectedHex, $res->getRgbaHexColor());
    }

    #[DataProvider('noColor')]
    public function testGetColorObjectNoColor(string $color): void
    {
        $pdf = $this->getTestObject();
        $this->assertNull($pdf->getColorObject($color));
    }

    /**
     * This accessor returns null on a parse error, unlike getColorObj().
     */
    #[DataProvider('malformed')]
    public function testGetColorObjectMalformed(string $color): void
    {
        $pdf = $this->getTestObject();
        $this->assertNull($pdf->getColorObject($color));
    }

    /**
     * Spot color names resolve through the read-only accessor too.
     */
    public function testGetColorObjectResolvesDefaultSpotColors(): void
    {
        $pdf = $this->getTestObject();

        $none = $pdf->getColorObject('none');
        $this->assertNotNull($none);
        $this->assertSame('0.000000 0.000000 0.000000 0.000000 k' . "\n", $none->getPdfColor());

        $all = $pdf->getColorObject('all');
        $this->assertNotNull($all);
        $this->assertSame('1.000000 1.000000 1.000000 1.000000 k' . "\n", $all->getPdfColor());
    }

    /**
     * Non-spot colors resolve to a device color operator. Spot lookup is
     * disabled so that names shared with the default spot table take the device
     * path.
     */
    #[DataProvider('pdfOperators')]
    public function testGetPdfColor(string $color, string $expectedOperator, float $delta): void
    {
        $pdf = $this->getTestObject();
        $this->bcAssertSamePdfOperator($expectedOperator, $pdf->getPdfColor($color, false, 1, false), $delta);
    }

    /**
     * The stroking variant emits the same operands with uppercase operators.
     */
    #[DataProvider('pdfOperators')]
    public function testGetPdfStrokeColor(string $color, string $expectedOperator, float $delta): void
    {
        $pdf = $this->getTestObject();
        $this->bcAssertSamePdfOperator(
            \strtoupper($expectedOperator),
            $pdf->getPdfStrokeColor($color, 1, false),
            $delta,
        );
    }

    /**
     * The filling variant is the non-stroking default.
     */
    #[DataProvider('pdfOperators')]
    public function testGetPdfFillColor(string $color, string $expectedOperator, float $delta): void
    {
        $pdf = $this->getTestObject();
        $this->bcAssertSamePdfOperator($expectedOperator, $pdf->getPdfFillColor($color, 1, false), $delta);
    }

    #[DataProvider('noColor')]
    public function testGetPdfColorNoColor(string $color): void
    {
        $pdf = $this->getTestObject();
        $this->assertSame('', $pdf->getPdfColor($color, false, 1, false));
    }

    #[DataProvider('malformed')]
    public function testGetPdfColorMalformed(string $color): void
    {
        $pdf = $this->getTestObject();
        $this->assertSame('', $pdf->getPdfColor($color, false, 1, false));
    }

    /**
     * A spot color name resolves to a Separation color space reference.
     */
    public function testGetPdfColorSpot(): void
    {
        $pdf = $this->getTestObject();
        $this->assertSame('/CS1 cs 1.000000 scn' . "\n", $pdf->getPdfColor('magenta', false, 1));
        $this->assertSame('/CS1 CS 1.000000 SCN' . "\n", $pdf->getPdfColor('magenta', true, 1));
        $this->assertSame('/CS1 cs 0.500000 scn' . "\n", $pdf->getPdfColor('magenta', false, 0.5));
        $this->assertSame('/CS1 CS 0.500000 SCN' . "\n", $pdf->getPdfColor('magenta', true, 0.5));
        $this->assertSame('/CS1 CS 0.500000 SCN' . "\n", $pdf->getPdfStrokeColor('magenta', 0.5));
        $this->assertSame('/CS1 cs 0.500000 scn' . "\n", $pdf->getPdfFillColor('magenta', 0.5));
    }

    /**
     * With spot lookup left enabled (the default), a name that is not a spot
     * color falls through to the device color path.
     */
    #[DataProvider('pdfOperators')]
    public function testGetPdfColorFallsBackToDeviceColor(string $color, string $expectedOperator, float $delta): void
    {
        $pdf = $this->getTestObject();
        $this->bcAssertSamePdfOperator($expectedOperator, $pdf->getPdfColor($color), $delta);
        // none of these names is a spot color, so nothing was registered
        $this->assertSame([], $pdf->getSpotColors());
    }

    /**
     * The spot color tint is clamped into [0..1].
     */
    public function testGetPdfColorClampsTint(): void
    {
        $pdf = $this->getTestObject();

        $this->assertSame('/CS1 cs 0.000000 scn' . "\n", $pdf->getPdfColor('magenta', false, 0));
        $this->assertSame('/CS1 cs 1.000000 scn' . "\n", $pdf->getPdfColor('magenta', false, 1));
        $this->assertSame('/CS1 cs 1.000000 scn' . "\n", $pdf->getPdfColor('magenta', false, 2.5));
        $this->assertSame('/CS1 cs 0.000000 scn' . "\n", $pdf->getPdfColor('magenta', false, -1));
        $this->assertSame('/CS1 CS 1.000000 SCN' . "\n", $pdf->getPdfColor('magenta', true, 99));
    }

    /**
     * Several default spot color names are also CSS color names and resolve to
     * a Separation. $allowSpot = false forces a device color and leaves the
     * spot registry untouched.
     */
    public function testGetPdfColorCanSkipSpotLookup(): void
    {
        $withSpot = $this->getTestObject();
        $this->assertSame('/CS1 cs 1.000000 scn' . "\n", $withSpot->getPdfColor('red'));
        $this->assertArrayHasKey('red', $withSpot->getSpotColors());

        $withoutSpot = $this->getTestObject();
        $this->assertSame('1.000000 0.000000 0.000000 rg' . "\n", $withoutSpot->getPdfColor('red', false, 1, false));
        $this->assertSame([], $withoutSpot->getSpotColors());

        $this->assertSame('1.000000 0.000000 0.000000 RG' . "\n", $withoutSpot->getPdfStrokeColor('red', 1, false));
        $this->assertSame('1.000000 0.000000 0.000000 rg' . "\n", $withoutSpot->getPdfFillColor('red', 1, false));
        $this->assertSame([], $withoutSpot->getSpotColors());

        // a name that is only a spot color yields nothing when spots are skipped
        $this->assertSame('', $withoutSpot->getPdfColor('all', false, 1, false));
    }

    public function testGetPdfRgbComponents(): void
    {
        $pdf = $this->getTestObject();
        $res = $pdf->getPdfRgbComponents('');
        $this->assertSame('', $res);

        $res = $pdf->getPdfRgbComponents('red');
        $this->assertSame('1.000000 0.000000 0.000000', $res);

        $res = $pdf->getPdfRgbComponents('#00ff00');
        $this->assertSame('0.000000 1.000000 0.000000', $res);

        $res = $pdf->getPdfRgbComponents('rgb(0,0,255)');
        $this->assertSame('0.000000 0.000000 1.000000', $res);
    }

    public function testGetPdfCmykComponents(): void
    {
        $pdf = $this->getTestObject();
        $res = $pdf->getPdfCmykComponents('');
        $this->assertSame('', $res);

        $res = $pdf->getPdfCmykComponents('red');
        $this->assertSame('0.000000 1.000000 1.000000 0.000000', $res);

        $res = $pdf->getPdfCmykComponents('rgb(64,128,191)');
        $this->assertSame('0.664921 0.329843 0.000000 0.250980', $res);

        $res = $pdf->getPdfCmykComponents('cyan');
        $this->assertSame('1.000000 0.000000 0.000000 0.000000', $res);
    }

    /**
     * Eight of the eleven default spot color names are also CSS color names.
     * They convert to the same color except 'green': the spot Green is
     * CMYK(1,0,1,0) = #00ff00, while CSS green is #008000. $allowSpot = false
     * selects the CSS reading. 'key', 'all' and 'none' are spot-only and do not
     * resolve with $allowSpot = false.
     */
    public function testAllowSpotSelectsBetweenSpotAndCssNames(): void
    {
        $pdf = $this->getTestObject();

        $this->assertSame('0.000000 1.000000 0.000000', $pdf->getPdfRgbComponents('green'));
        $this->assertSame('0.000000 0.501961 0.000000', $pdf->getPdfRgbComponents('green', false));

        $this->assertSame('1.000000 0.000000 1.000000 0.000000', $pdf->getPdfCmykComponents('green'));
        $this->assertSame('1.000000 0.000000 1.000000 0.498039', $pdf->getPdfCmykComponents('green', false));

        $this->assertSame('CMYK', $pdf->getColorObject('green')?->getType());
        $this->assertSame('#00ff00', $pdf->getColorObject('green')?->getRgbHexColor());
        $this->assertSame('RGB', $pdf->getColorObject('green', false)?->getType());
        $this->assertSame('#008000', $pdf->getColorObject('green', false)?->getRgbHexColor());

        // the other seven shared names agree either way; CMYK is compared as
        // well because with K = 1 the C, M and Y of Black do not reach RGB
        foreach (['red', 'blue', 'cyan', 'magenta', 'yellow', 'black', 'white'] as $name) {
            $this->assertSame($pdf->getPdfRgbComponents($name, false), $pdf->getPdfRgbComponents($name), $name);
        }

        $this->assertSame('0.000000 0.000000 0.000000 1.000000', $pdf->getPdfCmykComponents('black'));
        $this->assertSame('0.000000 0.000000 0.000000 0.000000', $pdf->getPdfCmykComponents('white'));
        $this->assertSame('1.000000 1.000000 1.000000 1.000000', $pdf->getPdfCmykComponents('all'));
        $this->assertSame('0.000000 0.000000 0.000000 1.000000', $pdf->getPdfCmykComponents('key'));
        $this->assertSame('0.000000 0.000000 0.000000 0.000000', $pdf->getPdfCmykComponents('none'));

        // the read accessors still have no side effect
        $this->assertSame([], $pdf->getSpotColors());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testReadAccessorsDoNotRegisterSpotColors(): void
    {
        $pdf = $this->getTestObject();

        // querying components for a default spot color name does not register it
        $pdf->getColorObject('red');
        $pdf->getPdfRgbComponents('green');
        $pdf->getPdfCmykComponents('cyan');
        $this->assertSame([], $pdf->getSpotColors());

        $pon = 0;
        $this->assertSame('', $pdf->getPdfSpotObjects($pon));
        $this->assertSame(0, $pon);
        $this->assertSame('', $pdf->getPdfSpotResources());

        // getPdfColor() emits a separation and does register
        $pdf->getPdfColor('magenta');
        $this->assertArrayHasKey('magenta', $pdf->getSpotColors());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetColorObjectResolvesRegisteredSpotColor(): void
    {
        $pdf = $this->getTestObject();

        // a custom spot color is not in the default table, so the read-only
        // accessor resolves it from the internal registry
        $cmyk = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0.1,
            'magenta' => 0.2,
            'yellow' => 0.3,
            'key' => 0.4,
            'alpha' => 1.0,
        ]);
        $pdf->addSpotColor('My Custom Spot', $cmyk);

        $res = $pdf->getColorObject('My Custom Spot');
        $this->assertNotNull($res);
        $this->assertNotSame($cmyk, $res);
        $this->assertEquals($cmyk, $res);

        // the read-only lookup added no registration
        $spotColors = $pdf->getSpotColors();
        $this->assertCount(1, $spotColors);
        $this->assertArrayHasKey('mycustomspot', $spotColors);
    }

    /**
     * The tint is the operand of 'scn', which only a Separation color space has;
     * a device color is written at full intensity.
     */
    public function testTintAppliesToSpotColorsOnly(): void
    {
        $pdf = new \Com\Tecnick\Color\Pdf();

        $this->assertSame("/CS1 cs 0.500000 scn\n", $pdf->getPdfColor('red', false, 0.5));
        $this->assertSame("/CS1 CS 0.250000 SCN\n", $pdf->getPdfStrokeColor('red', 0.25));

        $full = "1.000000 0.000000 0.000000 rg\n";
        $this->assertSame($full, $pdf->getPdfColor('#ff0000', false, 0.5));
        $this->assertSame($full, $pdf->getPdfColor('#ff0000', false, 0.0));
        $this->assertSame($full, $pdf->getPdfFillColor('#ff0000', 0.25));
        $this->assertSame($full, $pdf->getPdfColor('red', false, 0.5, false));
    }

    /**
     * Every accessor reaching into the spot registry hands back a copy, so
     * inverting the result leaves the registered color unchanged.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testSpotColorAccessorsDoNotLeakTheRegisteredModel(): void
    {
        $pdf = new \Com\Tecnick\Color\Pdf();
        $pdf->addSpotColor('Brand', new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0.1,
            'magenta' => 0.2,
            'yellow' => 0.3,
            'key' => 0.4,
        ]));
        $pdf->addSpotLabColor('Brand Lab', 50.0, 10.0, -20.0);

        $expectedCmyk = '0.100000 0.200000 0.300000 0.400000';
        $expectedLab = [
            'lstar' => 50.0,
            'astar' => 10.0,
            'bstar' => -20.0,
            'alpha' => 1.0,
        ];

        $pdf->getSpotColorObj('Brand')->invertColor();
        $pdf->getSpotLabColorObj('Brand Lab')->invertColor();
        $pdf->getColorObject('Brand')?->invertColor();

        $spotColors = $pdf->getSpotColors();
        $brand = $spotColors['brand'] ?? null;
        $brandLab = $spotColors['brandlab'] ?? null;
        $this->assertIsArray($brand);
        $this->assertIsArray($brandLab);
        $brandLabMeta = $brandLab['lab'];
        $this->assertIsArray($brandLabMeta);
        $brand['color']->invertColor();
        $brandLabMeta['model']->invertColor();

        $this->assertSame($expectedCmyk, $pdf->getSpotColorObj('Brand')->getComponentsString());
        $this->assertSame($expectedLab, $pdf->getSpotLabColorObj('Brand Lab')->toLabArray());

        $pon = 0;
        $out = $pdf->getPdfSpotObjects($pon);
        $this->assertStringContainsString('/C1 [' . $expectedCmyk . ']', $out);
        $this->assertStringContainsString('/C1 [50.000000 10.000000 -20.000000]', $out);
    }
}
