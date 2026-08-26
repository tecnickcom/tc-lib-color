<?php

declare(strict_types=1);

/**
 * WebTest.php
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
 * Web Color class test
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class WebTest extends TestUtil
{
    protected function getTestObject(): \Com\Tecnick\Color\Web
    {
        return new \Com\Tecnick\Color\Web();
    }

    /**
     * The shared table projected to the columns this test uses.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function parsable(): array
    {
        return \array_map(static fn(array $case): array => [$case[0], $case[1]], ColorStringProvider::parsable());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function noColor(): array
    {
        return ColorStringProvider::noColor();
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function malformed(): array
    {
        return ColorStringProvider::malformed();
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetHexFromName(): void
    {
        $web = $this->getTestObject();
        $res = $web->getHexFromName('aliceblue');
        $this->assertSame('f0f8ffff', $res);
        $res = $web->getHexFromName('color.yellowgreen');
        $this->assertSame('9acd32ff', $res);
        $res = $web->getHexFromName('rebeccapurple');
        $this->assertSame('663399ff', $res);
        $res = $web->getHexFromName('mediumpurple');
        $this->assertSame('9370dbff', $res);
        $res = $web->getHexFromName('palevioletred');
        $this->assertSame('db7093ff', $res);
        // the lookup is case-insensitive
        $res = $web->getHexFromName('AliceBlue');
        $this->assertSame('f0f8ffff', $res);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetHexFromNameInvalid(): void
    {
        $web = $this->getTestObject();
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to find the color hex for the name: invalid',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->getHexFromName('invalid'),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetNameFromHex(): void
    {
        $web = $this->getTestObject();
        $res = $web->getNameFromHex('f0f8ffff');
        $this->assertSame('aliceblue', $res);
        $res = $web->getNameFromHex('9acd32ff');
        $this->assertSame('yellowgreen', $res);
    }

    /**
     * Shape of every entry in the color table: a bare lowercase name mapped to
     * an opaque 8-digit lowercase hash.
     */
    public function testWebHexEntriesAreWellFormed(): void
    {
        $this->assertCount(150, \Com\Tecnick\Color\Web::WEBHEX);

        foreach (\Com\Tecnick\Color\Web::WEBHEX as $name => $hex) {
            $this->assertMatchesRegularExpression('/^[a-z]+$/', $name, 'malformed color name: ' . $name);
            $this->assertMatchesRegularExpression('/^[0-9a-f]{6}ff$/', $hex, 'malformed hash for ' . $name);
        }
    }

    /**
     * Every entry is reachable by name and resolves back to itself.
     *
     * Nine hex codes are shared by two or three names (aqua/cyan, gray/grey,
     * ...), so a reverse lookup only has to land on a name carrying the same
     * value.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testEveryWebHexEntryRoundTrips(): void
    {
        $web = $this->getTestObject();

        foreach (\Com\Tecnick\Color\Web::WEBHEX as $name => $hex) {
            $this->assertSame($hex, $web->getHexFromName($name), $name);
            $this->assertSame('#' . $hex, $web->getRgbObjFromName($name)->getRgbaHexColor(), $name);

            $closest = $web->getClosestWebColorFromString('#' . \substr($hex, 0, 6));
            $this->assertSame($hex, $web->getHexFromName($closest), $name . ' -> ' . $closest);
        }
    }

    /**
     * Every value in the table, checked against a list transcribed from CSS
     * Color Level 4.
     */
    public function testWebHexMatchesTheCssColorLevel4List(): void
    {
        $reference = \Test\NamedColorProvider::cssNamedColors();
        $aliases = \Test\NamedColorProvider::acrobatAliases();
        $table = \Com\Tecnick\Color\Web::WEBHEX;

        $this->assertCount(148, $reference);
        $this->assertCount(\count($reference) + \count($aliases), $table);

        foreach ($reference as $name => $hex) {
            $this->assertArrayHasKey($name, $table, $name);
            $this->assertSame($hex . 'ff', $table[$name] ?? '', $name);
        }

        // dkgray and ltgray are Acrobat JavaScript spellings, the only two
        // entries outside CSS Color Level 4, and duplicate a CSS name
        foreach ($aliases as $alias => $canonical) {
            $this->assertArrayHasKey($alias, $table, $alias);
            $this->assertSame($table[$canonical] ?? '', $table[$alias] ?? '', $alias);
        }

        $this->assertSame([], \array_diff_key($table, $reference, $aliases));
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testNonCssAliasesMatchTheirCanonicalName(): void
    {
        $web = $this->getTestObject();

        $this->assertSame($web->getHexFromName('darkgray'), $web->getHexFromName('dkgray'));
        $this->assertSame($web->getHexFromName('lightgray'), $web->getHexFromName('ltgray'));
    }

    /**
     * Several colors share a hex code. The reverse lookups scan WEBHEX in
     * insertion order, so the CSS name comes first.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testReverseLookupPrefersCanonicalCssName(): void
    {
        $web = $this->getTestObject();

        $this->assertSame('darkgray', $web->getNameFromHex('#a9a9a9'));
        $this->assertSame('lightgray', $web->getNameFromHex('#d3d3d3'));

        $this->assertSame('darkgray', $web->getClosestWebColorFromString('#a9a9a9'));
        $this->assertSame('lightgray', $web->getClosestWebColorFromString('#d3d3d4'));
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetNameFromHexBad(): void
    {
        $web = $this->getTestObject();
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to find the color name for the hex code: 012345',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->getNameFromHex('012345'),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testExtractHexCode(): void
    {
        $web = $this->getTestObject();
        $res = $web->extractHexCode('abc');
        $this->assertSame('aabbccff', $res);
        $res = $web->extractHexCode('#abc');
        $this->assertSame('aabbccff', $res);
        $res = $web->extractHexCode('abcd');
        $this->assertSame('aabbccdd', $res);
        $res = $web->extractHexCode('#abcd');
        $this->assertSame('aabbccdd', $res);
        $res = $web->extractHexCode('112233');
        $this->assertSame('112233ff', $res);
        $res = $web->extractHexCode('#112233');
        $this->assertSame('112233ff', $res);
        $res = $web->extractHexCode('11223344');
        $this->assertSame('11223344', $res);
        $res = $web->extractHexCode('#11223344');
        $this->assertSame('11223344', $res);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testExtractHexCodeBad(): void
    {
        $web = $this->getTestObject();
        // rejected by the regex, before any length check
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to extract the color hash: ',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->extractHexCode(''),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testExtractHexCodeNonHexDigits(): void
    {
        $web = $this->getTestObject();
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to extract the color hash: #gggggg',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->extractHexCode('#gggggg'),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testExtractHexCodeInvalidLength(): void
    {
        $web = $this->getTestObject();
        // 5 hexadecimal digits pass the regex and are rejected by the length check
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unsupported color hash length (5): 12345',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->extractHexCode('12345'),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testExtractHexCodeInvalidLength7(): void
    {
        $web = $this->getTestObject();
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unsupported color hash length (7): 1234567',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->extractHexCode('1234567'),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetRgbObjFromHex(): void
    {
        $web = $this->getTestObject();
        $rgb = $web->getRgbObjFromHex('#87ceebff');
        $this->assertSame('#87ceebff', $rgb->getRgbaHexColor());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetRgbObjFromHexBad(): void
    {
        $web = $this->getTestObject();
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to extract the color hash: xx',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->getRgbObjFromHex('xx'),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetRgbObjFromName(): void
    {
        $web = $this->getTestObject();
        $rgb = $web->getRgbObjFromName('skyblue');
        $this->assertSame('#87ceebff', $rgb->getRgbaHexColor());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetRgbObjFromNameBad(): void
    {
        $web = $this->getTestObject();
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unable to find the color hex for the name: xx',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->getRgbObjFromName('xx'),
        );
    }

    public function testNormalizeValue(): void
    {
        $web = $this->getTestObject();
        $res = $web->normalizeValue('50%', 50);
        $this->assertSame(0.5, $res);
        $res = $web->normalizeValue(128, 255);
        $this->bcAssertEqualsWithDelta(128 / 255, $res);
    }

    /**
     * A non-positive reference value yields 0.0.
     */
    public function testNormalizeValueNonPositiveMax(): void
    {
        $web = $this->getTestObject();
        $this->assertSame(0.0, $web->normalizeValue(1, 0));
        $this->assertSame(0.0, $web->normalizeValue('1', 0));
        $this->assertSame(0.0, $web->normalizeValue(1.0, -1));
        // a percentage does not use the reference value at all
        $this->assertSame(0.5, $web->normalizeValue('50%', 0));

        // 1 is the smallest reference value that is not rejected
        $this->assertSame(0.4, $web->normalizeValue(0.4, 1));
        $this->assertSame(1.0, $web->normalizeValue(1, 1));
    }

    public function testNormalizeValueClamping(): void
    {
        $web = $this->getTestObject();
        $this->assertSame(1.0, $web->normalizeValue(300, 255));
        $this->assertSame(0.0, $web->normalizeValue(-5, 255));
        $this->assertSame(1.0, $web->normalizeValue('150%', 255));
        $this->assertSame(0.0, $web->normalizeValue('-10%', 255));
        $this->assertSame(0.0, $web->normalizeValue(0, 255));
        $this->assertSame(1.0, $web->normalizeValue(255, 255));
    }

    public function testNormalizeValueInvalidPercent(): void
    {
        $web = $this->getTestObject();
        $this->assertSame(0.0, $web->normalizeValue('abc%', 255));
    }

    public function testNormalizeValueUnsupportedType(): void
    {
        $web = $this->getTestObject();
        $this->assertSame(0.0, $web->normalizeValue([], 255));
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    #[DataProvider('parsable')]
    public function testGetColorObj(string $color, string $expectedHex): void
    {
        $web = $this->getTestObject();
        $res = $web->getColorObj($color);
        $this->assertNotNull($res);
        $this->assertSame($expectedHex, $res->getRgbaHexColor());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    #[DataProvider('noColor')]
    public function testGetColorObjNoColor(string $color): void
    {
        $web = $this->getTestObject();
        $this->assertNull($web->getColorObj($color));
    }

    /**
     * A javascript color whose model letter is present but not one of t/g/r/c.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetColorObjFromJsUnknownModel(): void
    {
        $web = $this->getTestObject();
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'invalid javascript color: ["x"]',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->getColorObj('["X"]'),
        );
    }

    /**
     * A digit/dot run such as "1.2.3" is not a number and is rejected.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testNonNumericComponentsAreRejected(): void
    {
        $web = $this->getTestObject();

        foreach ([
            'lab(1.2.3 0 -39)',
            'rgba(64,128,191,1.2.3)',
            'rgba(64,128,191,1.2.3%)',
            'rgb(1.2.3%,4,5)',
            'g(.)',
            'hsl(1.2.3deg,50%,50%)',
        ] as $color) {
            $this->bcAssertThrows(
                \Com\Tecnick\Color\Exception::class,
                'invalid css color: ' . $color,
                /** @throws \Com\Tecnick\Color\Exception */
                static fn() => $web->getColorObj($color),
            );
        }
    }

    /**
     * Out-of-range component values are clamped, as CSS Color Level 4 requires.
     * Hue is an angle and wraps instead.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testNegativeComponentsAreClampedNotRejected(): void
    {
        $web = $this->getTestObject();

        $rgb = $web->getColorObj('rgb(-10,128,191)');
        $this->assertNotNull($rgb);
        $this->assertSame('#0080bfff', $rgb->getRgbaHexColor());

        $gray = $web->getColorObj('g(-5)');
        $this->assertNotNull($gray);
        $this->assertSame('#000000ff', $gray->getRgbaHexColor());

        $alpha = $web->getColorObj('rgba(64,128,191,-0.5)');
        $this->assertNotNull($alpha);
        $this->assertSame('#4080bf00', $alpha->getRgbaHexColor());

        $lab = $web->getColorObj('lab(-5 0 0)');
        $this->assertNotNull($lab);
        $this->assertSame(0.0, $lab->getArray()['L'] ?? null);

        // -150deg is the same hue as 210deg
        $hue = $web->getColorObj('hsl(-150,50%,50%)');
        $this->assertNotNull($hue);
        $this->assertSame('#4080bfff', $hue->getRgbaHexColor());
        $this->assertSame($web->getColorObj('hsl(210,50%,50%)')?->getRgbaHexColor(), $hue->getRgbaHexColor());
    }

    /**
     * Hue wraps: 400deg is the same color as 40deg. Saturation and lightness
     * stay clamped.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testHueWrapsAround(): void
    {
        $web = $this->getTestObject();

        $reference = $web->getColorObj('hsl(40,100%,50%)');
        $this->assertNotNull($reference);
        $this->assertSame('#ffaa00', $reference->getRgbHexColor());

        foreach (['hsl(400,100%,50%)', 'hsl(760,100%,50%)'] as $wrapped) {
            $res = $web->getColorObj($wrapped);
            $this->assertNotNull($res);
            $this->assertSame('#ffaa00', $res->getRgbHexColor(), $wrapped);
        }

        // a full turn is the same as no turn
        $full = $web->getColorObj('hsl(360,100%,50%)');
        $this->assertNotNull($full);
        $this->assertSame('#ff0000', $full->getRgbHexColor());

        // saturation and lightness are still clamped, not wrapped
        $clamped = $web->getColorObj('hsl(0,400%,50%)');
        $this->assertNotNull($clamped);
        $this->assertSame('#ff0000', $clamped->getRgbHexColor());
    }

    /**
     * Parsing the output of getCssColor() yields the same color back.
     *
     * @return array<array{0: string}>
     */
    public static function getCssRoundTripColors(): array
    {
        return [
            ['rgb(1,2,3)'],
            ['rgb(13,17,19)'],
            ['rgb(255,128,64)'],
            ['rgb(0,0,0)'],
            ['rgb(255,255,255)'],
            ['rgba(64,128,191,0.85)'],
            ['#010203'],
            ['#4080bf'],
            ['g(1)'],
            ['g(128)'],
            ['hsl(210,50%,50%)'],
            ['hsl(1,1%,1%)'],
            ['cmyk(67,33,0,25)'],
            ['cmyk(1,2,3,4)'],
            ['lab(52% 0 -39)'],
            ['mediumpurple'],
            ['rebeccapurple'],
        ];
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    #[DataProvider('getCssRoundTripColors')]
    public function testGetCssColorRoundTrips(string $color): void
    {
        $web = $this->getTestObject();

        $original = $web->getColorObj($color);
        $this->assertNotNull($original);

        $reparsed = $web->getColorObj($original->getCssColor());
        $this->assertNotNull($reparsed);

        $this->assertSame(
            $original->getRgbaHexColor(),
            $reparsed->getRgbaHexColor(),
            $color . ' -> ' . $original->getCssColor(),
        );
    }

    /**
     * Every model's CSS output survives the round-trip, for every 8-bit color
     * in the named table. The expectation is the original color. GRAY is
     * excluded from the color-preserving comparison because it discards chroma.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetCssColorRoundTripsForEveryModel(): void
    {
        $web = $this->getTestObject();

        foreach (\Com\Tecnick\Color\Web::WEBHEX as $name => $hex) {
            $rgb = $web->getRgbObjFromHex($hex);
            $models = [
                $rgb,
                new \Com\Tecnick\Color\Model\Hsl($rgb->toHslArray()),
                new \Com\Tecnick\Color\Model\Cmyk($rgb->toCmykArray()),
                new \Com\Tecnick\Color\Model\Lab($rgb->toLabArray()),
            ];

            foreach ($models as $model) {
                $reparsed = $web->getColorObj($model->getCssColor());
                $this->assertNotNull($reparsed);
                $this->assertSame(
                    $rgb->getRgbHexColor(),
                    $reparsed->getRgbHexColor(),
                    $name . ' as ' . $model->getType() . ': ' . $model->getCssColor(),
                );
            }

            // GRAY is lossy, so it round-trips against its own gray level
            $gray = new \Com\Tecnick\Color\Model\Gray($rgb->toGrayArray());
            $reparsedGray = $web->getColorObj($gray->getCssColor());
            $this->assertNotNull($reparsedGray);
            $this->assertSame(
                $gray->getRgbHexColor(),
                $reparsedGray->getRgbHexColor(),
                $name . ' as GRAY: ' . $gray->getCssColor(),
            );
        }
    }

    /**
     * Every malformed input is rejected with the message of the parser branch
     * that rejected it.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    #[DataProvider('malformed')]
    public function testGetColorObjMalformed(string $color, string $message): void
    {
        $web = $this->getTestObject();
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            $message,
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => $web->getColorObj($color),
        );
    }

    /**
     * A PCRE failure is reported with its own message, not as a syntax error.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetColorObjPcreFailure(): void
    {
        $limit = \ini_get('pcre.backtrack_limit');
        \ini_set('pcre.backtrack_limit', '1');

        try {
            $web = $this->getTestObject();
            $this->bcAssertThrows(
                \Com\Tecnick\Color\Exception::class,
                'unable to parse the color (Backtrack limit exhausted): rgb(64,128,191)',
                /** @throws \Com\Tecnick\Color\Exception */
                static fn() => $web->getColorObj('rgb(64,128,191)'),
            );
        } finally {
            \ini_set('pcre.backtrack_limit', $limit === false ? '1000000' : $limit);
        }
    }

    public function testTryGetColorObj(): void
    {
        $web = $this->getTestObject();

        $ok = $web->tryGetColorObj('royalblue');
        $this->assertNotNull($ok);
        $this->assertSame('#4169e1ff', $ok->getRgbaHexColor());

        $bad = $web->tryGetColorObj('g(-)');
        $this->assertNull($bad);
    }

    public function testGetRgbSquareDistance(): void
    {
        $web = $this->getTestObject();
        $cola = [
            'red' => 0,
            'green' => 0,
            'blue' => 0,
        ];
        $colb = [
            'red' => 1,
            'green' => 1,
            'blue' => 1,
        ];
        $dist = $web->getRgbSquareDistance($cola, $colb);
        $this->assertSame(3.0, $dist);

        $cola = [
            'red' => 0.5,
            'green' => 0.5,
            'blue' => 0.5,
        ];
        $colb = [
            'red' => 0.5,
            'green' => 0.5,
            'blue' => 0.5,
        ];
        $dist = $web->getRgbSquareDistance($cola, $colb);
        $this->assertSame(0.0, $dist);

        $cola = [
            'red' => 0.25,
            'green' => 0.50,
            'blue' => 0.75,
        ];
        $colb = [
            'red' => 0.50,
            'green' => 0.75,
            'blue' => 1.00,
        ];
        $dist = $web->getRgbSquareDistance($cola, $colb);
        $this->assertSame(0.1875, $dist);
    }

    public function testGetClosestWebColor(): void
    {
        $web = $this->getTestObject();
        $col = [
            'red' => 1,
            'green' => 0,
            'blue' => 0,
        ];
        $color = $web->getClosestWebColor($col);
        $this->assertSame('red', $color);

        $col = [
            'red' => 0,
            'green' => 1,
            'blue' => 0,
        ];
        $color = $web->getClosestWebColor($col);
        $this->assertSame('lime', $color);

        $col = [
            'red' => 0,
            'green' => 0,
            'blue' => 1,
        ];
        $color = $web->getClosestWebColor($col);
        $this->assertSame('blue', $color);

        $col = [
            'red' => 0.33,
            'green' => 0.4,
            'blue' => 0.18,
        ];
        $color = $web->getClosestWebColor($col);
        $this->assertSame('darkolivegreen', $color);

        // on an exact tie between duplicate hex codes (aqua/cyan both 00ffff),
        // the first name is returned
        $col = [
            'red' => 0,
            'green' => 1,
            'blue' => 1,
        ];
        $color = $web->getClosestWebColor($col);
        $this->assertSame('aqua', $color);
    }

    /**
     * Missing components default to 0, so an empty array is treated as black.
     */
    public function testGetClosestWebColorEmptyArray(): void
    {
        $web = $this->getTestObject();
        $this->assertSame('black', $web->getClosestWebColor([]));
    }

    public function testGetClosestWebColorFromString(): void
    {
        $web = $this->getTestObject();

        $color = $web->getClosestWebColorFromString('rgb(255,0,0)');
        $this->assertSame('red', $color);

        $color = $web->getClosestWebColorFromString('#0000ff');
        $this->assertSame('blue', $color);

        $color = $web->getClosestWebColorFromString('invalid-color-value');
        $this->assertSame('', $color);
    }

    /**
     * Every named color is its own perceptually closest match.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetClosestWebColorByDeltaEIsExactForEveryName(): void
    {
        $web = $this->getTestObject();

        foreach (\Com\Tecnick\Color\Web::WEBHEX as $name => $hex) {
            $match = $web->getClosestWebColorByDeltaEFromString($name);
            $this->assertSame($hex, \Com\Tecnick\Color\Web::WEBHEX[$match] ?? '', $name . ' matched ' . $match);
        }
    }

    /**
     * sRGB distance is not perceptually uniform, so the two matchers disagree
     * on colors that sit between named entries.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetClosestWebColorByDeltaEDiffersFromSrgbDistance(): void
    {
        $web = $this->getTestObject();

        foreach ([
            ['#9577a6', 'lightslategray', 'plum'],
            ['#93ca6b', 'darkseagreen',   'lightgreen'],
            ['#f9a98a', 'lightsalmon',    'darksalmon'],
            ['#5da78e', 'cadetblue',      'lightseagreen'],
            ['#45686e', 'dimgray',        'darkslategray'],
        ] as [$color, $srgbMatch, $labMatch]) {
            $this->assertSame($srgbMatch, $web->getClosestWebColorFromString($color), $color);
            $this->assertSame($labMatch, $web->getClosestWebColorByDeltaEFromString($color), $color);
        }
    }

    public function testGetClosestWebColorByDeltaEEdgeCases(): void
    {
        $web = $this->getTestObject();

        // missing components default to 0, i.e. lab(0 0 0) = black
        $this->assertSame('black', $web->getClosestWebColorByDeltaE([]));
        $this->assertSame('', $web->getClosestWebColorByDeltaEFromString('invalid-color-value'));
        $this->assertSame(0.0, $web->getLabSquareDistance([], []));
        $this->assertSame(25.0, $web->getLabSquareDistance(['lstar' => 3.0, 'astar' => 4.0, 'bstar' => 0.0], [
            'lstar' => 0.0,
            'astar' => 0.0,
            'bstar' => 0.0,
        ]));
    }
}
