<?php

declare(strict_types=1);

/**
 * ColorStringProvider.php
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

/**
 * Shared table of color definition strings and their expected parse results.
 *
 * Consumed by WebTest and PdfTest.
 *
 * @since     2026-08-26
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-color
 */
final class ColorStringProvider
{
    /**
     * Color strings that parse to a color, with the expected #RRGGBBAA
     * representation, the expected non-stroking PDF operator and the tolerance
     * its operands are compared with.
     *
     * @return array<string, array{0: string, 1: string, 2: string, 3: float}>
     */
    public static function parsable(): array
    {
        return [
            'hex rgba' => ['#1a2b3c4d', '#1a2b3c4d', "0.101961 0.168627 0.235294 rg\n", 0.0],
            'hex rgb' => ['#1a2b3c', '#1a2b3cff', "0.101961 0.168627 0.235294 rg\n", 0.0],
            'hex uppercase' => ['#1A2B3C', '#1a2b3cff', "0.101961 0.168627 0.235294 rg\n", 0.0],
            'hex padded' => ['  #1a2b3c  ', '#1a2b3cff', "0.101961 0.168627 0.235294 rg\n", 0.0],
            'css rgb uppercase' => ['RGB(64,128,191)', '#4080bfff', "0.250980 0.501961 0.749020 rg\n", 0.0],
            'css rgb padded' => [' rgb(64,128,191) ', '#4080bfff', "0.250980 0.501961 0.749020 rg\n", 0.0],
            'name uppercase' => ['RoyalBlue', '#4169e1ff', "0.254902 0.411765 0.882353 rg\n", 0.0],
            'name padded' => [" royalblue\t", '#4169e1ff', "0.254902 0.411765 0.882353 rg\n", 0.0],
            'hex short rgba' => ['#1234', '#11223344', "0.066667 0.133333 0.200000 rg\n", 0.0],
            'hex short rgb' => ['#123', '#112233ff', "0.066667 0.133333 0.200000 rg\n", 0.0],
            'name' => ['royalblue', '#4169e1ff', "0.254902 0.411765 0.882353 rg\n", 0.0],
            'name with parent' => ['color.royalblue', '#4169e1ff', "0.254902 0.411765 0.882353 rg\n", 0.0],
            'js gray' => ['["G",0.5]', '#808080ff', "0.500000 g\n", 0.0],
            'js rgb' => ['["RGB",0.25,0.50,0.75]', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'js cmyk' => ['["CMYK",0.666,0.333,0,0.25]', '#4080bfff', "0.666000 0.333000 0.000000 0.250000 k\n", 0.0],
            'css gray percent' => ['g(50%)', '#808080ff', "0.500000 g\n", 0.0],
            'css gray byte' => ['g(128)', '#808080ff', "0.501961 g\n", 0.0],
            'css rgb percent' => ['rgb(25%,50%,75%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css rgb byte' => ['rgb(64,128,191)', '#4080bfff', "0.250980 0.501961 0.749020 rg\n", 0.0],
            'css rgb space' => ['rgb(64 128 191)', '#4080bfff', "0.250980 0.501961 0.749020 rg\n", 0.0],
            'css rgb slash alpha' => ['rgb(64 128 191 / 50%)', '#4080bf80', "0.250980 0.501961 0.749020 rg\n", 0.0],
            'css rgba percent' => ['rgba(25%,50%,75%,0.85)', '#4080bfd9', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css rgba byte' => ['rgba(64,128,191,0.85)', '#4080bfd9', "0.250980 0.501961 0.749020 rg\n", 0.0],
            'css rgba percent alpha' => ['rgba(64,128,191,50%)', '#4080bf80', "0.250980 0.501961 0.749020 rg\n", 0.0],
            'css hsl' => ['hsl(210,50%,50%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsl bare' => ['hsl(210,50,50)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsl decimal' => ['hsl(210,50.0%,50.0%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsl space' => ['hsl(210 50% 50%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsl deg' => ['hsl(210deg,50%,50%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsl grad' => ['hsl(233.333333grad,50%,50%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsl rad' => ['hsl(2.0943951rad,50%,50%)', '#40bf40ff', "0.250000 0.750000 0.250000 rg\n", 0.0],
            'css hsl turn' => ['hsl(0.5833333turn 50% 50%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsl percent hue' => ['hsl(210%,50%,50%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsl negative hue' => ['hsl(-150,50%,50%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsl negative deg hue' => ['hsl(-150deg,50%,50%)', '#4080bfff', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css rgb negative clamped' => ['rgb(-10,128,191)', '#0080bfff', "0.000000 0.501961 0.749020 rg\n", 0.0],
            'css lab negative lstar clamped' => ['lab(-5 0 0)', '#000000ff', "0.000000 0.000000 0.000000 rg\n", 1e-5],
            'css hsla' => ['hsla(210,50%,50%,0.85)', '#4080bfd9', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css hsla percent alpha' => ['hsla(210,50%,50%,50%)', '#4080bf80', "0.250000 0.500000 0.750000 rg\n", 0.0],
            'css cmyk percent' => ['cmyk(67%,33%,0,25%)', '#3f80bfff', "0.670000 0.330000 0.000000 0.250000 k\n", 0.0],
            'css cmyk bare' => ['cmyk(67,33,0,25)', '#3f80bfff', "0.670000 0.330000 0.000000 0.250000 k\n", 0.0],
            'css cmyk space' => ['cmyk(67% 33% 0% 25%)', '#3f80bfff', "0.670000 0.330000 0.000000 0.250000 k\n", 0.0],
            'css cmyk decimal' => [
                'cmyk(66.5%,33%,0,25%)',
                '#4080bfff',
                "0.665000 0.330000 0.000000 0.250000 k\n",
                0.0,
            ],
            // four distinct components, so that each channel scale is covered
            'css cmyk distinct' => ['cmyk(10,20,30,40)', '#8a7a6bff', "0.100000 0.200000 0.300000 0.400000 k\n", 0.0],
            'css cmyka bare' => ['cmyka(67,33,0,25,0.85)', '#3f80bfd9', "0.670000 0.330000 0.000000 0.250000 k\n", 0.0],
            'css cmyka percent' => [
                'cmyka(67%,33%,0,25%,0.85)',
                '#3f80bfd9',
                "0.670000 0.330000 0.000000 0.250000 k\n",
                0.0,
            ],
            'css lab' => ['lab(52% 0 -39)', '#407fbfff', "0.252784 0.499848 0.747328 rg\n", 1e-5],
            'css lab alpha' => ['lab(52% 0 -39 / 0.85)', '#407fbfd9', "0.252784 0.499848 0.747328 rg\n", 1e-5],
            'css lab bare with percent alpha' => [
                'lab(52 0 -39 / 85%)',
                '#407fbfd9',
                "0.252784 0.499848 0.747328 rg\n",
                1e-5,
            ],
        ];
    }

    /**
     * Color strings that legitimately denote "no color".
     *
     * @return array<string, array{0: string}>
     */
    public static function noColor(): array
    {
        return [
            'empty' => [''],
            'css transparent' => ['t()'],
            'js transparent' => ['["T"]'],
            'name transparent' => ['transparent'],
            'name transparent with parent' => ['color.transparent'],
        ];
    }

    /**
     * Malformed color strings that the parser rejects, with the exact error
     * message identifying the rejecting branch.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function malformed(): array
    {
        return [
            'css gray' => ['g(-)', 'invalid css color: g(-)'],
            'css rgb' => ['rgb(-)', 'invalid css color: rgb(-)'],
            'css hsl' => ['hsl(-)', 'invalid css color: hsl(-)'],
            'css cmyk' => ['cmyk(-)', 'invalid css color: cmyk(-)'],
            'css lab' => ['lab(-)', 'invalid css color: lab(-)'],
            'css trailing garbage' => ['rgb(64,128,191)x', 'invalid css color: rgb(64,128,191)x'],
            'css second group' => ['rgb(x)(64,128,191)', 'invalid css color: rgb(x)(64,128,191)'],
            'css too many components' => ['rgb(1,2,3,4,5)', 'invalid css color: rgb(1,2,3,4,5)'],
            'css gray digit dot run' => ['g(...)', 'invalid css color: g(...)'],
            'css rgb digit dot run' => ['rgb(1.2.3,4,5)', 'invalid css color: rgb(1.2.3,4,5)'],
            'css hsl digit dot run' => ['hsl(1.2.3,50%,50%)', 'invalid css color: hsl(1.2.3,50%,50%)'],
            'css cmyk digit dot run' => ['cmyk(1.2.3,0,0,0)', 'invalid css color: cmyk(1.2.3,0,0,0)'],
            'css lab digit dot run' => ['lab(1.2.3 0 -39)', 'invalid css color: lab(1.2.3 0 -39)'],
            'css alpha digit dot run' => [
                'rgba(64,128,191,1.2.3)',
                'invalid css color: rgba(64,128,191,1.2.3)',
            ],
            'js digit dot run' => ['["G",1.2.3]', 'invalid javascript color: ["g",1.2.3]'],
            'css comma then space' => ['rgb(64,128 191)', 'mixed css color separators: rgb(64,128 191)'],
            'css space then comma' => ['rgb(64 128,191)', 'mixed css color separators: rgb(64 128,191)'],
            'css space with comma alpha' => [
                'rgb(64 128 191, 0.5)',
                'mixed css color separators: rgb(64 128 191, 0.5)',
            ],
            'css slash alpha with commas' => [
                'rgb(64,128,191 / 0.5)',
                'mixed css color separators: rgb(64,128,191 / 0.5)',
            ],
            'css transparent with components' => ['t(64,128,191)', 'invalid css color: t(64,128,191)'],
            'js transparent with components' => ['["T",1]', 'invalid javascript color: ["t",1]'],
            'js gray without components' => ['["G"]', 'invalid javascript color: ["g"]'],
            'js rgb without components' => ['["RGB"]', 'invalid javascript color: ["rgb"]'],
            'js cmyk without components' => ['["CMYK"]', 'invalid javascript color: ["cmyk"]'],
            'js unknown model' => ['["X"]', 'invalid javascript color: ["x"]'],
            'js truncated' => ['[*', 'invalid javascript color: [*'],
            'js trailing garbage' => ['["G",0.5]x', 'invalid javascript color: ["g",0.5]x'],
            'bare hash' => ['#', 'unable to extract the color hash: #'],
            'unknown name' => ['not-a-color', 'unable to find the color hex for the name: not-a-color'],
            'unsupported function' => ['hwb(120 30% 40%)', 'unsupported color syntax: hwb(120 30% 40%)'],
            'unsupported function with dot' => ['oklab(0.5 0.1 0.1)', 'unsupported color syntax: oklab(0.5 0.1 0.1)'],
        ];
    }
}
