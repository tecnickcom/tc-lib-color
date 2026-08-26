<?php

declare(strict_types=1);

/**
 * SpotTest.php
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

use Com\Tecnick\Color\Model\Lab;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * Spot Color class test
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class SpotTest extends TestUtil
{
    protected function getTestObject(): \Com\Tecnick\Color\Spot
    {
        return new \Com\Tecnick\Color\Spot();
    }

    public function testGetSpotColors(): void
    {
        $spot = $this->getTestObject();
        $res = $spot->getSpotColors();
        $this->assertSame(0, \count($res));
    }

    public function testNormalizeSpotColorName(): void
    {
        $spot = $this->getTestObject();
        $res = $spot->normalizeSpotColorName('abc.FG12!-345');
        $this->assertSame('abcfg12345', $res);
    }

    /**
     * A name with no alphanumeric character normalizes to the empty key, which
     * matches no default spot color.
     */
    public function testNormalizeSpotColorNameWithoutAlphanumerics(): void
    {
        $spot = $this->getTestObject();
        $this->assertSame('', $spot->normalizeSpotColorName('!!!'));
        $this->assertSame('', $spot->normalizeSpotColorName(''));

        // both the lookup and the registration paths name the input rather than
        // the empty key it normalizes to
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'invalid spot color name: !!!',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $spot->getSpotColor('!!!'),
        );

        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'invalid spot color name: ',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $spot->getSpotColor(''),
        );

        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'invalid spot color name: !!!',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $spot->addSpotColorFromArray('!!!', []),
        );
    }

    public function testEncodeSpotColorNameEmpty(): void
    {
        $spot = $this->getTestObject();
        $this->assertSame('', $spot->encodeSpotColorName(''));
    }

    public function testEncodeSpotColorName(): void
    {
        $spot = $this->getTestObject();
        // spaces and uppercase letters are preserved/encoded, not stripped
        $this->assertSame('SPOTTYPE#20279#20C', $spot->encodeSpotColorName('SPOTTYPE 279 C'));
        // plain ASCII names are returned unchanged
        $this->assertSame('Reflex#20Blue', $spot->encodeSpotColorName('Reflex Blue'));
        // the number sign and the PDF delimiters are escaped
        $this->assertSame('a#23b#28c#29#2Fd', $spot->encodeSpotColorName('a#b(c)/d'));
        // tabs and other control/whitespace bytes are escaped
        $this->assertSame('x#09y', $spot->encodeSpotColorName("x\ty"));
        // bytes outside printable ASCII (e.g. UTF-8 "é") are escaped per byte
        $this->assertSame('caf#C3#A9', $spot->encodeSpotColorName('café'));
    }

    /**
     * Every PDF delimiter is escaped, as ISO 32000-1, 7.3.5 requires.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function pdfNameCharProvider(): array
    {
        return [
            'number sign' => ['a#b', 'a#23b'],
            'left parenthesis' => ['a(b', 'a#28b'],
            'right parenthesis' => ['a)b', 'a#29b'],
            'less than' => ['a<b', 'a#3Cb'],
            'greater than' => ['a>b', 'a#3Eb'],
            'left square bracket' => ['a[b', 'a#5Bb'],
            'right square bracket' => ['a]b', 'a#5Db'],
            'left curly brace' => ['a{b', 'a#7Bb'],
            'right curly brace' => ['a}b', 'a#7Db'],
            'solidus' => ['a/b', 'a#2Fb'],
            'percent sign' => ['a%b', 'a#25b'],
            'all delimiters' => ['a<b>c[d]e{f}g%h', 'a#3Cb#3Ec#5Bd#5De#7Bf#7Dg#25h'],
            // regular character boundaries: 0x21 and 0x7E are regular, 0x20 and 0x7F are not
            'space (0x20)' => ["a\x20b", 'a#20b'],
            'exclamation mark (0x21)' => ["a\x21b", 'a!b'],
            'tilde (0x7E)' => ["a\x7Eb", 'a~b'],
            'delete (0x7F)' => ["a\x7Fb", 'a#7Fb'],
            'null (0x00)' => ["a\x00b", 'a#00b'],
            'high byte (0xFF)' => ["a\xFFb", 'a#FFb'],
        ];
    }

    #[DataProvider('pdfNameCharProvider')]
    public function testEncodeSpotColorNameEscapesEveryDelimiter(string $name, string $expected): void
    {
        $spot = $this->getTestObject();
        $this->assertSame($expected, $spot->encodeSpotColorName($name));
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetPdfSpotObjectsEncodesName(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotColorFromArray('SPOTTYPE 279 C', [
            'cyan' => 0.66,
            'magenta' => 0.28,
            'yellow' => 0,
            'key' => 0,
            'alpha' => 1,
        ]);

        $obj = 1;
        $res = $spot->getPdfSpotObjects($obj);
        $this->assertStringContainsString('[/Separation /SPOTTYPE#20279#20C /DeviceCMYK', $res);
        $this->assertStringNotContainsString('SPOTTYPE279c', $res);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetSpotColor(): void
    {
        $spot = $this->getTestObject();
        $res = $spot->getSpotColor('none');
        $this->assertSame('0.000000 0.000000 0.000000 0.000000 k' . "\n", $res['color']->getPdfColor());
        $res = $spot->getSpotColor('all');
        $this->assertSame('1.000000 1.000000 1.000000 1.000000 k' . "\n", $res['color']->getPdfColor());
        $res = $spot->getSpotColor('red');
        $this->assertSame('0.000000 1.000000 1.000000 0.000000 K' . "\n", $res['color']->getPdfColor(true));
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetSpotColorNotFound(): void
    {
        $spot = $this->getTestObject();
        // the normalized key, not the raw name, is reported
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to find the spot color: missingcolor',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $spot->getSpotColor('missing-color'),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetSpotColorObj(): void
    {
        $spot = $this->getTestObject();
        $res = $spot->getSpotColorObj('none');
        $this->assertSame('0.000000 0.000000 0.000000 0.000000 k' . "\n", $res->getPdfColor());
        $res = $spot->getSpotColorObj('all');
        $this->assertSame('1.000000 1.000000 1.000000 1.000000 k' . "\n", $res->getPdfColor());
        $res = $spot->getSpotColorObj('red');
        $this->assertSame('0.000000 1.000000 1.000000 0.000000 K' . "\n", $res->getPdfColor(true));
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testAddSpotColor(): void
    {
        $spot = $this->getTestObject();
        $cmyk = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0.666,
            'magenta' => 0.333,
            'yellow' => 0,
            'key' => 0.25,
            'alpha' => 0.85,
        ]);
        $spot->addSpotColor('test', $cmyk);
        $res = $spot->getSpotColors();
        $this->assertArrayHasKey('test', $res);
        $testColor = $res['test'] ?? null;
        $this->assertNotNull($testColor);

        $this->assertSame(1, $testColor['i']);
        $this->assertSame('test', $testColor['name']);
        $this->assertSame('0.666000 0.333000 0.000000 0.250000 k' . "\n", $testColor['color']->getPdfColor());

        // test overwrite
        $cmyk = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0.25,
            'magenta' => 0.35,
            'yellow' => 0.45,
            'key' => 0.55,
            'alpha' => 0.65,
        ]);
        $key = $spot->addSpotColor('test', $cmyk);
        $this->assertSame('test', $key);

        $res = $spot->getSpotColors();
        $this->assertArrayHasKey('test', $res);
        $testColor = $res['test'] ?? null;
        $this->assertNotNull($testColor);

        $this->assertSame(1, $testColor['i']);
        $this->assertSame('test', $testColor['name']);
        $this->assertSame('0.250000 0.350000 0.450000 0.550000 k' . "\n", $testColor['color']->getPdfColor());
    }

    /**
     * A name with no letter or digit is rejected.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testAddSpotColorEmptyNameThrows(): void
    {
        $spot = $this->getTestObject();
        $cmyk = new \Com\Tecnick\Color\Model\Cmyk(['cyan' => 1]);

        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'invalid spot color name: ---',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn(): string => $spot->addSpotColor('---', $cmyk),
        );

        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'invalid spot color name: ',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn(): string => $spot->addSpotLabColor('', 50.0, 0.0, 0.0),
        );

        $this->assertSame([], $spot->getSpotColors());
    }

    /**
     * Once the PDF object has been written, the color can no longer be
     * redefined.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testRedefineEmittedSpotColorThrows(): void
    {
        $spot = $this->getTestObject();
        $cmyk = new \Com\Tecnick\Color\Model\Cmyk(['cyan' => 1]);
        $spot->addSpotColor('test', $cmyk);

        $obj = 1;
        $spot->getPdfSpotObjects($obj);
        $this->assertSame('/ColorSpace << /CS1 2 0 R >>' . "\n", $spot->getPdfSpotResources());

        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to redefine an emitted spot color: test',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn(): string => $spot->addSpotColor('Test', $cmyk),
        );

        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to redefine an emitted spot color: test',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn(): string => $spot->addSpotLabColor('test', 50.0, 0.0, 0.0),
        );

        // the emitted object is still the one the resource dictionary points at
        $this->assertSame('/ColorSpace << /CS1 2 0 R >>' . "\n", $spot->getPdfSpotResources());
        $this->assertSame('', $spot->getPdfSpotObjects($obj));
        $this->assertSame(2, $obj);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testAddSpotColorFromArray(): void
    {
        $spot = $this->getTestObject();
        $key = $spot->addSpotColorFromArray('Brand Spot', [
            'cyan' => 0.666,
            'magenta' => 0.333,
            'yellow' => 0,
            'key' => 0.25,
            'alpha' => 0.85,
        ]);

        $this->assertSame('brandspot', $key);
        $res = $spot->getSpotColor('Brand Spot');
        $this->assertSame('0.666000 0.333000 0.000000 0.250000 k' . "\n", $res['color']->getPdfColor());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testAddSpotLabColor(): void
    {
        $spot = $this->getTestObject();
        $key = $spot->addSpotLabColor('Brand Orange', 64.25, 58.5, 71.2);
        $this->assertSame('brandorange', $key);

        $res = $spot->getSpotColor('Brand Orange');
        $this->assertSame(1, $res['i']);
        $this->assertSame('Brand Orange', $res['name']);
        $this->assertSame('Lab', $res['space']);
        $this->assertInstanceOf(\Com\Tecnick\Color\Model\Cmyk::class, $res['color']);
        $this->assertIsArray($res['lab']);
        $this->assertSame([0.9505, 1.0, 1.089], $res['lab']['whitepoint']);
        $this->assertSame([100.0, 0.0, 0.0], $res['lab']['c0']);
        $this->assertInstanceOf(\Com\Tecnick\Color\Model\Lab::class, $res['lab']['model']);
        $this->bcAssertEqualsWithDelta([
            'lstar' => 64.25,
            'astar' => 58.5,
            'bstar' => 71.2,
            'alpha' => 1,
        ], $res['lab']['model']->toLabArray());
    }

    /**
     * The optional trailing arrays override the defaults positionally:
     * whitepoint, blackpoint, range, col0.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testAddSpotLabColorWithOptions(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotLabColor(
            'Custom Lab',
            50.0,
            10.0,
            -20.0,
            [0.9, 1.0, 1.1],
            [0.01, 0.02, 0.03],
            [-100.0, 100.0, -90.0, 90.0],
            [120.0, 200.0, -300.0],
        );

        $res = $spot->getSpotColor('Custom Lab');
        $this->assertIsArray($res['lab']);
        $this->assertSame([0.9, 1.0, 1.1], $res['lab']['whitepoint']);
        $this->assertSame([0.01, 0.02, 0.03], $res['lab']['blackpoint']);
        $this->assertSame([-100.0, 100.0, -90.0, 90.0], $res['lab']['range']);
        // col0 is clamped: L* to [0..100], a* to range[0..1], b* to range[2..3]
        $this->assertSame([100.0, 100.0, -90.0], $res['lab']['c0']);

        $obj = 0;
        $out = $spot->getPdfSpotObjects($obj);
        $this->assertStringContainsString('/WhitePoint [0.900000 1.000000 1.100000]', $out);
        $this->assertStringContainsString('/BlackPoint [0.010000 0.020000 0.030000]', $out);
        $this->assertStringContainsString('/Range [-100.000000 100.000000 -90.000000 90.000000]', $out);
        $this->assertStringContainsString('/C0 [100.000000 100.000000 -90.000000]', $out);
        // the tint transform declares the same bounds as the color space
        $this->assertStringContainsString(
            '/Range [0.000000 100.000000 -100.000000 100.000000 -90.000000 90.000000]',
            $out,
        );
    }

    /**
     * A range wider than the Lab model is narrowed, so /Range, /C0 and /C1
     * agree.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testAddSpotLabColorNarrowsARangeWiderThanTheModel(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotLabColor(
            'Wide',
            50.0,
            200.0,
            -200.0,
            [0.9505, 1.0, 1.089],
            [0.0, 0.0, 0.0],
            [-200.0, 200.0, -200.0, 200.0],
            [100.0, 150.0, -150.0],
        );

        $res = $spot->getSpotColor('Wide');
        $this->assertIsArray($res['lab']);
        $this->assertSame([-128.0, 127.0, -128.0, 127.0], $res['lab']['range']);
        $this->assertSame([100.0, 127.0, -128.0], $res['lab']['c0']);

        $obj = 0;
        $out = $spot->getPdfSpotObjects($obj);
        $this->assertStringContainsString('/Range [-128.000000 127.000000 -128.000000 127.000000]>>]', $out);
        $this->assertStringContainsString('/C0 [100.000000 127.000000 -128.000000]', $out);
        $this->assertStringContainsString('/C1 [50.000000 127.000000 -128.000000]', $out);
    }

    /**
     * A partially supplied option array falls back to the default per element.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testAddSpotLabColorWithPartialOptions(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotLabColor('Partial Lab', 50.0, 10.0, -20.0, [0.8]);

        $res = $spot->getSpotColor('Partial Lab');
        $this->assertIsArray($res['lab']);
        $this->assertSame([0.8, 1.0, 1.089], $res['lab']['whitepoint']);
        // the untouched options keep their defaults
        $this->assertSame([0.0, 0.0, 0.0], $res['lab']['blackpoint']);
        $this->assertSame([-128.0, 127.0, -128.0, 127.0], $res['lab']['range']);
    }

    /**
     * The a* and b* components are clamped to the supplied range.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testAddSpotLabColorClampsToRange(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotLabColor(
            'Clamped Lab',
            150.0,
            80.0,
            -80.0,
            [0.9505, 1.0, 1.089],
            [0.0, 0.0, 0.0],
            [-10.0, 20.0, -30.0, 40.0],
        );

        $res = $spot->getSpotColor('Clamped Lab');
        $this->assertIsArray($res['lab']);
        $this->bcAssertEqualsWithDelta([
            'lstar' => 100.0,
            'astar' => 20.0,
            'bstar' => -30.0,
            'alpha' => 1,
        ], $res['lab']['model']->toLabArray());
    }

    /**
     * The whitepoint only affects the emitted PDF Lab metadata; the stored CMYK
     * equivalent is computed from the Lab values using the D65 whitepoint.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testAddSpotLabColorWhitepointDoesNotAffectCmykFallback(): void
    {
        $withDefault = $this->getTestObject();
        $withDefault->addSpotLabColor('Ref', 64.25, 58.5, 71.2);

        $withCustom = $this->getTestObject();
        $withCustom->addSpotLabColor('Ref', 64.25, 58.5, 71.2, [0.5, 0.6, 0.7]);

        $this->assertSame(
            $withDefault->getSpotColorObj('Ref')->getPdfColor(),
            $withCustom->getSpotColorObj('Ref')->getPdfColor(),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetSpotLabColorObj(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotLabColor('Brand Orange', 64.25, 58.5, 71.2);

        $res = $spot->getSpotLabColorObj('Brand Orange');
        $this->assertInstanceOf(\Com\Tecnick\Color\Model\Lab::class, $res);
        $this->bcAssertEqualsWithDelta([
            'lstar' => 64.25,
            'astar' => 58.5,
            'bstar' => 71.2,
            'alpha' => 1,
        ], $res->toLabArray());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetSpotColorObjFromLab(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotLabColor('Brand Orange', 64.25, 58.5, 71.2);

        $cmyk = $spot->getSpotColorObj('Brand Orange');
        $this->assertInstanceOf(\Com\Tecnick\Color\Model\Cmyk::class, $cmyk);
        $this->assertSame('0.000000 0.596375 0.949051 0.000000 k' . "\n", $cmyk->getPdfColor());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetSpotLabColorObjFromCmyk(): void
    {
        $spot = $this->getTestObject();
        $spot->getSpotColor('cyan');

        $lab = $spot->getSpotLabColorObj('cyan');
        $this->assertInstanceOf(\Com\Tecnick\Color\Model\Lab::class, $lab);
    }

    public function testGetPdfSpotObjectsEmpty(): void
    {
        $spot = $this->getTestObject();
        $obj = 1;
        $res = $spot->getPdfSpotObjects($obj);
        $this->assertSame(1, $obj);
        $this->assertSame('', $res);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetPdfSpotResourcesEmpty(): void
    {
        $spot = $this->getTestObject();
        $pdfSpotResources = $spot->getPdfSpotResources();
        $this->assertSame('', $pdfSpotResources);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetPdfSpotObjects(): void
    {
        $spot = $this->getTestObject();
        $cmyk = new \Com\Tecnick\Color\Model\Cmyk([
            'cyan' => 0.666,
            'magenta' => 0.333,
            'yellow' => 0,
            'key' => 0.25,
            'alpha' => 0.85,
        ]);
        $spot->addSpotColor('test', $cmyk);
        $spot->getSpotColor('cyan');
        $spot->getSpotColor('magenta');
        $spot->getSpotColor('yellow');
        $spot->getSpotColor('key');

        $obj = 1;
        $res = $spot->getPdfSpotObjects($obj);
        $this->assertSame(6, $obj);
        $this->assertSame(
            '2 0 obj'
            . "\n"
            . '[/Separation /test /DeviceCMYK <</Range [0 1 0 1 0 1 0 1] /C0 [0 0 0 0]'
            . ' /C1 [0.666000 0.333000 0.000000 0.250000] /FunctionType 2 /Domain [0 1] /N 1>>]'
            . "\n"
            . 'endobj'
            . "\n"
            . '3 0 obj'
            . "\n"
            . '[/Separation /Cyan /DeviceCMYK <</Range [0 1 0 1 0 1 0 1] /C0 [0 0 0 0]'
            . ' /C1 [1.000000 0.000000 0.000000 0.000000] /FunctionType 2 /Domain [0 1] /N 1>>]'
            . "\n"
            . 'endobj'
            . "\n"
            . '4 0 obj'
            . "\n"
            . '[/Separation /Magenta /DeviceCMYK <</Range [0 1 0 1 0 1 0 1] /C0 [0 0 0 0]'
            . ' /C1 [0.000000 1.000000 0.000000 0.000000] /FunctionType 2 /Domain [0 1] /N 1>>]'
            . "\n"
            . 'endobj'
            . "\n"
            . '5 0 obj'
            . "\n"
            . '[/Separation /Yellow /DeviceCMYK <</Range [0 1 0 1 0 1 0 1] /C0 [0 0 0 0]'
            . ' /C1 [0.000000 0.000000 1.000000 0.000000] /FunctionType 2 /Domain [0 1] /N 1>>]'
            . "\n"
            . 'endobj'
            . "\n"
            . '6 0 obj'
            . "\n"
            . '[/Separation /Key /DeviceCMYK <</Range [0 1 0 1 0 1 0 1] /C0 [0 0 0 0]'
            . ' /C1 [0.000000 0.000000 0.000000 1.000000] /FunctionType 2 /Domain [0 1] /N 1>>]'
            . "\n"
            . 'endobj'
            . "\n",
            $res,
        );

        $res = $spot->getPdfSpotResources();
        $this->assertSame('/ColorSpace << /CS1 2 0 R /CS2 3 0 R /CS3 4 0 R /CS4 5 0 R /CS5 6 0 R >>' . "\n", $res);

        $resk = $spot->getPdfSpotResourcesByKeys(['cyan', 'yellow']);
        $this->assertSame('/ColorSpace << /CS2 3 0 R /CS4 5 0 R >>' . "\n", $resk);

        $resk_empty = $spot->getPdfSpotResourcesByKeys([]);
        $this->assertSame('', $resk_empty);
    }

    /**
     * An unregistered key raises.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetPdfSpotResourcesByUnknownKeyThrows(): void
    {
        $spot = $this->getTestObject();
        $spot->getSpotColor('cyan');
        $obj = 0;
        $spot->getPdfSpotObjects($obj);

        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to find the spot color: nonexistent',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $spot->getPdfSpotResourcesByKeys(['cyan', 'nonexistent']),
        );
    }

    /**
     * A registered spot color that has not been emitted has no PDF object to
     * point at, so the resource writers raise.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetPdfSpotResourcesRejectsUnemittedEntries(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotColorFromArray('My Spot', [
            'cyan' => 1,
            'magenta' => 0,
            'yellow' => 0,
            'key' => 0,
            'alpha' => 1,
        ]);

        // no object emitted yet
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to reference a spot color that has no PDF object, call getPdfSpotObjects() first: myspot',
            $spot->getPdfSpotResources(...),
        );
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to reference a spot color that has no PDF object, call getPdfSpotObjects() first: myspot',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $spot->getPdfSpotResourcesByKeys(['myspot']),
        );

        $obj = 10;
        $spot->getPdfSpotObjects($obj);
        $this->assertSame('/ColorSpace << /CS1 11 0 R >>' . "\n", $spot->getPdfSpotResources());
    }

    /**
     * A spot color first used after the PDF objects were written is emitted by
     * the next getPdfSpotObjects() call.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testSpotColorRegisteredAfterEmissionIsReported(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotColorFromArray('First', [
            'cyan' => 1,
            'magenta' => 0,
            'yellow' => 0,
            'key' => 0,
            'alpha' => 1,
        ]);

        $obj = 0;
        $spot->getPdfSpotObjects($obj);
        $this->assertSame('/ColorSpace << /CS1 1 0 R >>' . "\n", $spot->getPdfSpotResources());

        // used only after the objects were written
        $spot->getSpotColor('blue');
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to reference a spot color that has no PDF object, call getPdfSpotObjects() first: blue',
            $spot->getPdfSpotResources(...),
        );

        // emitting again resolves it
        $spot->getPdfSpotObjects($obj);
        $this->assertSame('/ColorSpace << /CS1 1 0 R /CS2 2 0 R >>' . "\n", $spot->getPdfSpotResources());
    }

    /**
     * An already-emitted entry is skipped and consumes no object number.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetPdfSpotObjectsIsIdempotent(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotColorFromArray('My Spot', [
            'cyan' => 1,
            'magenta' => 0,
            'yellow' => 0,
            'key' => 0,
            'alpha' => 1,
        ]);

        $obj = 10;
        $first = $spot->getPdfSpotObjects($obj);
        $this->assertStringContainsString('11 0 obj', $first);
        $this->assertSame(11, $obj);

        $this->assertSame('', $spot->getPdfSpotObjects($obj));
        $this->assertSame(11, $obj);
        $this->assertSame('/ColorSpace << /CS1 11 0 R >>' . "\n", $spot->getPdfSpotResources());

        // a spot color added later is still emitted on the next call
        $spot->addSpotColorFromArray('Other Spot', [
            'cyan' => 0,
            'magenta' => 1,
            'yellow' => 0,
            'key' => 0,
            'alpha' => 1,
        ]);
        $this->assertStringContainsString('12 0 obj', $spot->getPdfSpotObjects($obj));
        $this->assertSame(12, $obj);
        $this->assertSame('/ColorSpace << /CS1 11 0 R /CS2 12 0 R >>' . "\n", $spot->getPdfSpotResources());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetPdfSpotObjectsLab(): void
    {
        $spot = $this->getTestObject();
        $spot->addSpotLabColor('Brand Orange', 64.25, 58.5, 71.2);

        $obj = 7;
        $res = $spot->getPdfSpotObjects($obj);
        $this->assertSame(8, $obj);
        $this->assertSame(
            '8 0 obj'
            . "\n"
            . '[/Separation /Brand#20Orange [/Lab << /WhitePoint [0.950500 1.000000 1.089000]'
            . ' /BlackPoint [0.000000 0.000000 0.000000] /Range [-128.000000 127.000000 -128.000000 127.000000]>>] <<'
            . ' /FunctionType 2 /Domain [0 1]'
            . ' /Range [0.000000 100.000000 -128.000000 127.000000 -128.000000 127.000000]'
            . ' /C0 [100.000000 0.000000 0.000000]'
            . ' /C1 [64.250000 58.500000 71.200000] /N 1>>]'
            . "\n"
            . 'endobj'
            . "\n",
            $res,
        );

        $pdfSpotResources = $spot->getPdfSpotResources();
        $this->assertSame('/ColorSpace << /CS1 8 0 R >>' . "\n", $pdfSpotResources);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetPdfSpotObjectsSkipsInvalidNonCmykColor(): void
    {
        $spot = $this->getTestObject();

        $property = new ReflectionProperty($spot, 'spot_colors');
        $property->setValue($spot, [
            'invalid' => [
                'i' => 1,
                'n' => 0,
                'name' => 'Invalid',
                'color' => new Lab([
                    'lstar' => 50,
                    'astar' => 0,
                    'bstar' => 0,
                    'alpha' => 1,
                ]),
                'space' => 'DeviceCMYK',
                'lab' => null,
            ],
        ]);

        $obj = 10;
        // a non-CMYK, non-Lab entry is skipped: no object is emitted and the
        // PDF object counter is left unchanged
        $this->assertSame('', $spot->getPdfSpotObjects($obj));
        $this->assertSame(10, $obj);

        // the entry keeps no PDF object, so it can never be referenced as a
        // resource; the state is reachable only by writing to $spot_colors from
        // a subclass, the public API being typed to accept a Cmyk
        for ($i = 0; $i < 2; ++$i) {
            $this->bcAssertThrows(
                \Com\Tecnick\Color\Exception::class,
                'unable to reference a spot color that has no PDF object, call getPdfSpotObjects() first: invalid',
                $spot->getPdfSpotResources(...),
            );

            $this->assertSame('', $spot->getPdfSpotObjects($obj));
            $this->assertSame(10, $obj);
        }
    }
}
