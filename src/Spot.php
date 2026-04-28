<?php

/**
 * Spot.php
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link      https://github.com/tecnickcom/tc-lib-color
 *
 * This file is part of tc-lib-color software library.
 */

namespace Com\Tecnick\Color;

use Com\Tecnick\Color\Exception as ColorException;
use Com\Tecnick\Color\Model\Cmyk;
use Com\Tecnick\Color\Model\Lab;

/**
 * Com\Tecnick\Color\Spot
 *
 * Spot Color class
 *
 * @since     2015-02-21
 * @category  Library
 * @package   Color
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link      https://github.com/tecnickcom/tc-lib-color
 *
 * @phpstan-type TLabTriplet array{0: float, 1: float, 2: float}
 * @phpstan-type TLabRange array{0: float, 1: float, 2: float, 3: float}
 * @phpstan-type TSpotColor array{
 *                 'i': int,
 *                 'n': int,
 *                 'name': string,
 *                 'color': Cmyk,
 *                 'space': 'DeviceCMYK'|'Lab',
 *                 'lab': array{
 *                   'whitepoint': TLabTriplet,
 *                   'blackpoint': TLabTriplet,
 *                   'range': TLabRange,
 *                   'c0': TLabTriplet,
 *                   'model': Lab,
 *                 }|null,
 *               }
 */
class Spot extends \Com\Tecnick\Color\Web
{
    /**
     * Array of default Spot colors
     * Color keys must be in lowercase and without spaces.
     *
     * @var array<string, array{
     *       'name': string,
     *       'color': array{
     *           'cyan': int|float,
     *           'magenta': int|float,
     *           'yellow': int|float,
     *           'key': int|float,
     *           'alpha': int|float,
     *       }
     *     }>
     */
    public const DEFAULT_SPOT_COLORS = [
        'none' => [
            'name' => 'None',
            'color' => [
                'cyan' => 0,
                'magenta' => 0,
                'yellow' => 0,
                'key' => 0,
                'alpha' => 1,
            ],
        ],
        'all' => [
            'name' => 'All',
            'color' => [
                'cyan' => 1,
                'magenta' => 1,
                'yellow' => 1,
                'key' => 1,
                'alpha' => 1,
            ],
        ],
        'cyan' => [
            'name' => 'Cyan',
            'color' => [
                'cyan' => 1,
                'magenta' => 0,
                'yellow' => 0,
                'key' => 0,
                'alpha' => 1,
            ],
        ],
        'magenta' => [
            'name' => 'Magenta',
            'color' => [
                'cyan' => 0,
                'magenta' => 1,
                'yellow' => 0,
                'key' => 0,
                'alpha' => 1,
            ],
        ],
        'yellow' => [
            'name' => 'Yellow',
            'color' => [
                'cyan' => 0,
                'magenta' => 0,
                'yellow' => 1,
                'key' => 0,
                'alpha' => 1,
            ],
        ],
        'key' => [
            'name' => 'Key',
            'color' => [
                'cyan' => 0,
                'magenta' => 0,
                'yellow' => 0,
                'key' => 1,
                'alpha' => 1,
            ],
        ],
        'white' => [
            'name' => 'White',
            'color' => [
                'cyan' => 0,
                'magenta' => 0,
                'yellow' => 0,
                'key' => 0,
                'alpha' => 1,
            ],
        ],
        'black' => [
            'name' => 'Black',
            'color' => [
                'cyan' => 0,
                'magenta' => 0,
                'yellow' => 0,
                'key' => 1,
                'alpha' => 1,
            ],
        ],
        'red' => [
            'name' => 'Red',
            'color' => [
                'cyan' => 0,
                'magenta' => 1,
                'yellow' => 1,
                'key' => 0,
                'alpha' => 1,
            ],
        ],
        'green' => [
            'name' => 'Green',
            'color' => [
                'cyan' => 1,
                'magenta' => 0,
                'yellow' => 1,
                'key' => 0,
                'alpha' => 1,
            ],
        ],
        'blue' => [
            'name' => 'Blue',
            'color' => [
                'cyan' => 1,
                'magenta' => 1,
                'yellow' => 0,
                'key' => 0,
                'alpha' => 1,
            ],
        ],
    ];

    /**
     * Array of Spot colors
     *
     * @var array<string, TSpotColor>
     */
    protected $spot_colors = [];

    /**
     * Returns the array of spot colors.
     *
     * @return array<string, TSpotColor>
     */
    public function getSpotColors(): array
    {
        return $this->spot_colors;
    }

    /**
     * Return the normalized version of the spot color name
     *
     * @param string $name Full name of the spot color.
     */
    public function normalizeSpotColorName(string $name): string
    {
        $ret = \preg_replace('/[^a-z0-9]*+/', '', \strtolower($name));
        return $ret ?? '';
    }

    /**
     * Return the requested spot color data array
     *
     * @param string $name Full name of the spot color.
     *
     * @return TSpotColor
     *
     * @throws ColorException if the color is not found
     */
    public function getSpotColor(string $name): array
    {
        $key = $this->normalizeSpotColorName($name);
        if (empty($this->spot_colors[$key])) {
            // search on default spot colors
            if (empty(self::DEFAULT_SPOT_COLORS[$key])) {
                throw new ColorException('unable to find the spot color: ' . $key);
            }

            $this->addSpotColor($key, new Cmyk(self::DEFAULT_SPOT_COLORS[$key]['color']));
        }

        return $this->spot_colors[$key];
    }

    /**
     * Return the requested spot color CMYK object
     *
     * @param string $name Full name of the spot color.
     *
     * @throws ColorException if the color is not found
     */
    public function getSpotColorObj(string $name): Cmyk
    {
        return $this->getSpotColor($name)['color'];
    }

    /**
     * Return the requested spot color Lab object.
     *
     * @param string $name Full name of the spot color.
     *
     * @throws ColorException if the color is not found
     */
    public function getSpotLabColorObj(string $name): Lab
    {
        $spot = $this->getSpotColor($name);
        if (\is_array($spot['lab'])) {
            return $spot['lab']['model'];
        }

        return new Lab($spot['color']->toLabArray());
    }

    /**
     * Add a new spot color or overwrite an existing one with the same name.
     *
     * @param string $name Full name of the spot color.
     * @param Cmyk   $cmyk CMYK color object
     *
     * @return string Spot color key.
     */
    public function addSpotColor(string $name, Cmyk $cmyk): string
    {
        $key = $this->normalizeSpotColorName($name);
        $num = isset($this->spot_colors[$key]) ? $this->spot_colors[$key]['i'] : (\count($this->spot_colors) + 1);

        $this->spot_colors[$key] = [
            'i' => $num, // color index
            'n' => 0, // PDF object number
            'name' => $name, // color name (key)
            'color' => $cmyk, // CMYK color object
            'space' => 'DeviceCMYK', // alternate color space in PDF Separation
            'lab' => null, // optional Lab metadata
        ];

        return $key;
    }

    /**
     * Add a new Lab-based spot color or overwrite an existing one with the same name.
     *
     * @param string       $name       Full name of the spot color.
     * @param float        $lstar      Lab L* component in [0..100].
     * @param float        $astar      Lab a* component.
     * @param float        $bstar      Lab b* component.
     * @param TLabTriplet  $whitepoint CIE XYZ whitepoint.
     * @param TLabTriplet  $blackpoint CIE XYZ blackpoint.
     * @param TLabRange    $range      Lab a-star and b-star range [a_min, a_max, b_min, b_max].
     * @param TLabTriplet  $col0       Tint=0 Lab output.
     *
     * @return string Spot color key.
     */
    public function addSpotLabColor(
        string $name,
        float $lstar,
        float $astar,
        float $bstar,
        array $whitepoint = [0.9505, 1.0000, 1.0890],
        array $blackpoint = [0.0, 0.0, 0.0],
        array $range = [-128.0, 127.0, -128.0, 127.0],
        array $col0 = [100.0, 0.0, 0.0]
    ): string {
        $key = $this->normalizeSpotColorName($name);
        $num = isset($this->spot_colors[$key]) ? $this->spot_colors[$key]['i'] : (\count($this->spot_colors) + 1);

        $labRange = [
            (float) $range[0],
            (float) $range[1],
            (float) $range[2],
            (float) $range[3],
        ];

        $labModel = new Lab(
            [
                'lstar' => \max(0.0, \min(100.0, $lstar)),
                'astar' => \max($labRange[0], \min($labRange[1], $astar)),
                'bstar' => \max($labRange[2], \min($labRange[3], $bstar)),
                'alpha' => 1.0,
            ]
        );

        $this->spot_colors[$key] = [
            'i' => $num, // color index
            'n' => 0, // PDF object number
            'name' => $name, // color name (key)
            'color' => new Cmyk($labModel->toCmykArray()), // CMYK equivalent
            'space' => 'Lab', // alternate color space in PDF Separation
            'lab' => [
                'whitepoint' => [(float) $whitepoint[0], (float) $whitepoint[1], (float) $whitepoint[2]],
                'blackpoint' => [(float) $blackpoint[0], (float) $blackpoint[1], (float) $blackpoint[2]],
                'range' => $labRange,
                'c0' => [
                    \max(0.0, \min(100.0, (float) $col0[0])),
                    \max($labRange[0], \min($labRange[1], (float) $col0[1])),
                    \max($labRange[2], \min($labRange[3], (float) $col0[2])),
                ],
                'model' => $labModel,
            ],
        ];

        return $key;
    }

    /**
     * Returns a PDF-formatted numeric array.
     *
     * @param array<int, float> $values
     */
    private function getPdfNumArray(array $values): string
    {
        $out = [];
        foreach ($values as $value) {
            $out[] = \sprintf('%F', $value);
        }

        return '[' . \implode(' ', $out) . ']';
    }

    /**
     * Returns the PDF command to output Spot color objects.
     *
     * @param int $pon Current PDF object number
     *
     * @return string PDF command
     */
    public function getPdfSpotObjects(int &$pon): string
    {
        $out = '';
        foreach ($this->spot_colors as $name => $color) {
            $out .= (++$pon) . ' 0 obj' . "\n";
            $this->spot_colors[$name]['n'] = $pon;

            if ($color['space'] === 'Lab' && \is_array($color['lab'])) {
                $lab = $color['lab']['model']->toLabArray();
                $out .= '[/Separation /' . \str_replace(' ', '#20', $name)
                    . ' [/Lab <<'
                    . ' /WhitePoint ' . $this->getPdfNumArray($color['lab']['whitepoint'])
                    . ' /BlackPoint ' . $this->getPdfNumArray($color['lab']['blackpoint'])
                    . ' /Range ' . $this->getPdfNumArray($color['lab']['range'])
                    . '>>] <<'
                    . ' /FunctionType 2'
                    . ' /Domain [0 1]'
                    . ' /C0 ' . $this->getPdfNumArray($color['lab']['c0'])
                    . ' /C1 ' . $this->getPdfNumArray([
                        $lab['lstar'],
                        $lab['astar'],
                        $lab['bstar'],
                    ])
                    . ' /N 1'
                    . '>>]' . "\n"
                    . 'endobj' . "\n";
                continue;
            }

            if (! $color['color'] instanceof Cmyk) {
                continue;
            }

            $out .= '[/Separation /' . \str_replace(' ', '#20', $name)
                . ' /DeviceCMYK <<'
                . '/Range [0 1 0 1 0 1 0 1]'
                . ' /C0 [0 0 0 0]'
                . ' /C1 [' . $color['color']->getComponentsString() . ']'
                . ' /FunctionType 2'
                . ' /Domain [0 1]'
                . ' /N 1'
                . '>>]' . "\n"
                . 'endobj' . "\n";
        }

        return $out;
    }

    /**
     * Returns the PDF command to output the provided Spot color resources.
     *
     * @param array<string, array{'i': int, 'n': int}> $data Spot color array.
     *
     * @return string PDF command
     */
    private function getOutPdfSpotResources(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $out = '/ColorSpace <<';

        foreach ($data as $spot_color) {
            $out .= ' /CS' . $spot_color['i'] . ' ' . $spot_color['n'] . ' 0 R';
        }

        return $out . ' >>' . "\n";
    }

    /**
     * Returns the PDF command to output Spot color resources.
     *
     * @return string PDF command
     */
    public function getPdfSpotResources(): string
    {
        return $this->getOutPdfSpotResources($this->spot_colors);
    }

    /**
     * Returns the PDF command to output Spot color resources.
     *
     * @param array<string> $keys Array of font keys.
     *
     * @return string PDF command
     */
    public function getPdfSpotResourcesByKeys(array $keys): string
    {
        if (empty($keys)) {
            return '';
        }

        $data = [];
        foreach ($keys as $key) {
            $data[$key] = [
                'i' => $this->spot_colors[$key]['i'],
                'n' => $this->spot_colors[$key]['n'],
            ];
        }

        return $this->getOutPdfSpotResources($data);
    }
}
