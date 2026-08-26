<?php

declare(strict_types=1);

/**
 * TestUtil.php
 *
 * @since     2020-12-19
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

/**
 * Shared base class for the test suite: assertion helpers with float tolerances.
 *
 * @since     2020-12-19
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
class TestUtil extends \PHPUnit\Framework\TestCase
{
    /**
     * Assert that two values are equal within $delta.
     */
    public function bcAssertEqualsWithDelta(
        mixed $expected,
        mixed $actual,
        float $delta = 1e-6,
        string $message = '',
    ): void {
        parent::assertEqualsWithDelta($expected, $actual, $delta, $message);
    }

    /**
     * @param class-string<\Throwable> $exception
     */
    public function bcExpectException(string $exception): void
    {
        parent::expectException($exception);
    }

    /**
     * Assert that two PDF color operator strings match.
     *
     * The operands are compared within $delta, the operator letters exactly.
     */
    public function bcAssertSamePdfOperator(string $expected, string $actual, float $delta = 0.0): void
    {
        $this->bcAssertSameTokens('/\s+/', $expected, $actual, $delta);
    }

    /**
     * Assert that two Acrobat JavaScript color arrays match.
     *
     * The components are compared within $delta, the model name exactly.
     */
    public function bcAssertSameJsColor(string $expected, string $actual, float $delta = 1e-5): void
    {
        $this->bcAssertSameTokens('/[,\[\]]+/', $expected, $actual, $delta);
    }

    /**
     * Assert that two strings hold the same tokens, comparing the numeric ones
     * with a tolerance.
     */
    private function bcAssertSameTokens(string $separator, string $expected, string $actual, float $delta): void
    {
        $expectedTokens = \preg_split($separator, \trim($expected), -1, \PREG_SPLIT_NO_EMPTY);
        $actualTokens = \preg_split($separator, \trim($actual), -1, \PREG_SPLIT_NO_EMPTY);
        $this->assertIsArray($expectedTokens);
        $this->assertIsArray($actualTokens);

        $this->assertSameSize($expectedTokens, $actualTokens, $expected . ' != ' . $actual);

        foreach ($expectedTokens as $i => $token) {
            $other = $actualTokens[$i] ?? '';
            if (\is_numeric($token) && \is_numeric($other)) {
                $this->assertEqualsWithDelta((float) $token, (float) $other, $delta, $expected . ' != ' . $actual);
                continue;
            }

            $this->assertSame($token, $other, $expected . ' != ' . $actual);
        }
    }

    /**
     * Assert that $callback throws $exception carrying exactly $message.
     *
     * @param class-string<\Throwable> $exception
     */
    public function bcAssertThrows(string $exception, string $message, callable $callback): void
    {
        try {
            $callback();
        } catch (\Throwable $throwable) {
            $this->assertInstanceOf($exception, $throwable);
            $this->assertSame($message, $throwable->getMessage());
            return;
        }

        $this->fail($exception . ' was not thrown');
    }
}
