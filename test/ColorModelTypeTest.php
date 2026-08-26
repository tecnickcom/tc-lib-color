<?php

declare(strict_types=1);

/**
 * ColorModelTypeTest.php
 *
 * @since     2026-07-17
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

/**
 * ColorModelType enum test
 *
 * @since     2026-07-17
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class ColorModelTypeTest extends TestUtil
{
    public function testCaseBackingValues(): void
    {
        $this->assertSame('GRAY', ColorModelType::Gray->value);
        $this->assertSame('RGB', ColorModelType::Rgb->value);
        $this->assertSame('HSL', ColorModelType::Hsl->value);
        $this->assertSame('CMYK', ColorModelType::Cmyk->value);
        $this->assertSame('LAB', ColorModelType::Lab->value);
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testFromLooseCanonical(): void
    {
        $this->assertSame(ColorModelType::Gray, ColorModelType::fromLoose('GRAY'));
        $this->assertSame(ColorModelType::Rgb, ColorModelType::fromLoose('RGB'));
        $this->assertSame(ColorModelType::Hsl, ColorModelType::fromLoose('HSL'));
        $this->assertSame(ColorModelType::Cmyk, ColorModelType::fromLoose('CMYK'));
        $this->assertSame(ColorModelType::Lab, ColorModelType::fromLoose('LAB'));
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testFromLooseIsCaseInsensitive(): void
    {
        $this->assertSame(ColorModelType::Gray, ColorModelType::fromLoose('gray'));
        $this->assertSame(ColorModelType::Rgb, ColorModelType::fromLoose('rgb'));
        $this->assertSame(ColorModelType::Hsl, ColorModelType::fromLoose('Hsl'));
        $this->assertSame(ColorModelType::Cmyk, ColorModelType::fromLoose('cmyk'));
        $this->assertSame(ColorModelType::Lab, ColorModelType::fromLoose('Lab'));
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testFromLoosePassesThroughEnumInstance(): void
    {
        $this->assertSame(ColorModelType::Cmyk, ColorModelType::fromLoose(ColorModelType::Cmyk));
    }

    /**
     * Round-trip invariant: every case resolves back from its own backing value.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testFromLooseRoundTrip(): void
    {
        foreach (ColorModelType::cases() as $case) {
            $this->assertSame($case, ColorModelType::fromLoose($case->value));
        }
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testFromLooseUnknownThrows(): void
    {
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unknown color model type: unknown',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => ColorModelType::fromLoose('unknown'),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testFromLooseEmptyStringThrows(): void
    {
        $this->bcAssertThrows(
            \Com\Tecnick\Color\Exception::class,
            'unknown color model type: ',
            /** @throws \Com\Tecnick\Color\Exception */
            static fn() => ColorModelType::fromLoose(''),
        );
    }

    /**
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetTypeEnum(): void
    {
        $gray = new \Com\Tecnick\Color\Model\Gray([]);
        $this->assertSame(ColorModelType::Gray, $gray->getTypeEnum());

        $rgb = new \Com\Tecnick\Color\Model\Rgb([]);
        $this->assertSame(ColorModelType::Rgb, $rgb->getTypeEnum());

        $hsl = new \Com\Tecnick\Color\Model\Hsl([]);
        $this->assertSame(ColorModelType::Hsl, $hsl->getTypeEnum());

        $cmyk = new \Com\Tecnick\Color\Model\Cmyk([]);
        $this->assertSame(ColorModelType::Cmyk, $cmyk->getTypeEnum());

        $lab = new \Com\Tecnick\Color\Model\Lab([]);
        $this->assertSame(ColorModelType::Lab, $lab->getTypeEnum());
    }

    /**
     * The typed accessor agrees with the string accessor.
     *
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetTypeEnumMatchesGetType(): void
    {
        $rgb = new \Com\Tecnick\Color\Model\Rgb([]);
        $this->assertSame($rgb->getType(), $rgb->getTypeEnum()->value);
    }
}
