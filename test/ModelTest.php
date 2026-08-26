<?php

declare(strict_types=1);

/**
 * ModelTest.php
 *
 * @since     2026-08-26
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

use Com\Tecnick\Color\ColorModelType;
use Com\Tecnick\Color\Model;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Color Model base class test
 *
 * @since     2026-08-26
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class ModelTest extends TestUtil
{
    /**
     * @return array<array{0: string, 1: class-string<Model>}>
     */
    public static function getModelTypes(): array
    {
        return [
            ['GRAY', \Com\Tecnick\Color\Model\Gray::class],
            ['RGB', \Com\Tecnick\Color\Model\Rgb::class],
            ['HSL', \Com\Tecnick\Color\Model\Hsl::class],
            ['CMYK', \Com\Tecnick\Color\Model\Cmyk::class],
            ['LAB', \Com\Tecnick\Color\Model\Lab::class],
        ];
    }

    /**
     * @param class-string<Model> $expected
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    #[DataProvider('getModelTypes')]
    public function testCreateFromString(string $type, string $expected): void
    {
        $model = Model::create($type);
        $this->assertInstanceOf($expected, $model);
        $this->assertSame($type, $model->getType());
    }

    /**
     * @param class-string<Model> $expected
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    #[DataProvider('getModelTypes')]
    public function testCreateFromEnum(string $type, string $expected): void
    {
        $model = Model::create(ColorModelType::from($type));
        $this->assertInstanceOf($expected, $model);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testCreateWithComponents(): void
    {
        $model = Model::create(ColorModelType::Rgb, [
            'red' => 0.25,
            'green' => 0.5,
            'blue' => 0.75,
            'alpha' => 0.85,
        ]);
        $this->assertSame('#4080bfd9', $model->getRgbaHexColor());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testCreateIsCaseInsensitive(): void
    {
        $this->assertSame('RGB', Model::create('rgb')->getType());
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testCreateUnknownTypeThrows(): void
    {
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unknown color model type: cmy',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => Model::create('cmy'),
        );
    }

    /**
     * A component name that the model does not define is rejected.
     *
     * @return array<array{0: string, 1: array<string, float>, 2: string}>
     */
    public static function getUnknownComponents(): array
    {
        return [
            ['GRAY', ['gray' => 0.5, 'lstar' => 50.0], 'lstar'],
            ['RGB', ['red' => 1.0, 'blu' => 0.5], 'blu'],
            ['HSL', ['hue' => 0.5, 'saturation2' => 1.0], 'saturation2'],
            ['CMYK', ['cyan' => 1.0, 'black' => 1.0], 'black'],
            ['LAB', ['lstar' => 50.0, 'red' => 1.0], 'red'],
        ];
    }

    /**
     * @param array<string, float> $components
     */
    #[DataProvider('getUnknownComponents')]
    public function testUnknownComponentThrows(string $type, array $components, string $unknown): void
    {
        $this->bcAssertThrows(
            \Com\Tecnick\Color\UnknownComponentException::class,
            'unknown color components: ' . $unknown,
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => Model::create($type, $components),
        );
    }

    public function testUnknownComponentsAreAllReported(): void
    {
        $this->bcAssertThrows(
            \Com\Tecnick\Color\UnknownComponentException::class,
            'unknown color components: foo, bar',
            static fn() => new \Com\Tecnick\Color\Model\Rgb(['foo' => 1, 'bar' => 2]),
        );
    }

    /**
     * The lenient accessors do not swallow UnknownComponentException.
     */
    public function testUnknownComponentIsNotAColorException(): void
    {
        $exception = new \Com\Tecnick\Color\UnknownComponentException('x');
        $this->assertInstanceOf(\LogicException::class, $exception);
        $this->assertNotInstanceOf(\Com\Tecnick\Color\Exception::class, $exception);
    }

    /**
     * A value that is not a number at all falls back to the component default.
     */
    public function testNonNumericComponentFallsBackToDefault(): void
    {
        $rgb = new \Com\Tecnick\Color\Model\Rgb([
            'red' => 'abc',
            'green' => 0.5,
            'alpha' => 'x',
        ]);
        $this->assertSame(
            [
                'R' => 0.0,
                'G' => 0.5,
                'B' => 0.0,
                'A' => 1.0,
            ],
            $rgb->getArray(),
        );

        $hsl = new \Com\Tecnick\Color\Model\Hsl([
            'hue' => 'abc',
            'saturation' => 1.0,
        ]);
        $this->assertSame(0.0, $hsl->getArray()['H'] ?? 1.0);
    }

    /**
     * NAN falls back to the component default; INF and -INF are clamped.
     */
    public function testNanComponentFallsBackToDefault(): void
    {
        $rgb = new \Com\Tecnick\Color\Model\Rgb([
            'red' => \NAN,
            'green' => \INF,
            'blue' => -\INF,
            'alpha' => \NAN,
        ]);
        $this->assertSame(
            [
                'R' => 0.0,
                'G' => 1.0,
                'B' => 0.0,
                'A' => 1.0,
            ],
            $rgb->getArray(),
        );

        $lab = new \Com\Tecnick\Color\Model\Lab([
            'lstar' => \NAN,
            'astar' => \NAN,
            'bstar' => \NAN,
        ]);
        $this->assertSame(
            [
                'L' => 0.0,
                'a' => 0.0,
                'b' => 0.0,
                'A' => 1.0,
            ],
            $lab->getArray(),
        );

        $hsl = new \Com\Tecnick\Color\Model\Hsl(['hue' => \NAN]);
        $this->assertSame(0.0, $hsl->getArray()['H'] ?? 1.0);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetNormalizedValue(): void
    {
        $rgb = new \Com\Tecnick\Color\Model\Rgb([]);
        $this->assertSame(128.0, $rgb->getNormalizedValue(0.5, 255));
        $this->assertSame(0.0, $rgb->getNormalizedValue(0.0, 255));
        $this->assertSame(255.0, $rgb->getNormalizedValue(1.0, 255));
        // out-of-range fractions are clamped
        $this->assertSame(255.0, $rgb->getNormalizedValue(2.0, 255));
        $this->assertSame(0.0, $rgb->getNormalizedValue(-1.0, 255));
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetHexValue(): void
    {
        $rgb = new \Com\Tecnick\Color\Model\Rgb([]);
        $this->assertSame('80', $rgb->getHexValue(0.5, 255));
        $this->assertSame('00', $rgb->getHexValue(0.0, 255));
        $this->assertSame('ff', $rgb->getHexValue(1.0, 255));
    }

    /**
     * withInvertedColor() leaves the receiver untouched, unlike invertColor().
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testWithInvertedColorDoesNotMutateTheReceiver(): void
    {
        foreach (['GRAY', 'RGB', 'HSL', 'CMYK', 'LAB'] as $type) {
            $model = \Com\Tecnick\Color\Model::create($type, []);
            $original = $model->getArray();

            $inverted = $model->withInvertedColor();
            $this->assertNotSame($model, $inverted, $type);
            $this->assertSame($type, $inverted->getType(), $type);
            $this->assertSame($original, $model->getArray(), $type);

            // the copy carries the inverted color
            $mutated = \Com\Tecnick\Color\Model::create($type, []);
            $mutated->invertColor();
            $this->assertEquals($mutated->getArray(), $inverted->getArray(), $type);

            // applying it twice returns the original color
            $this->bcAssertEqualsWithDelta($original, $inverted->withInvertedColor()->getArray(), 1e-5, $type);
        }
    }
}
