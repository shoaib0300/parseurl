<?php

namespace Helper;

use Exception\InvalidStringException;

class UnicodeString
{
    public static function walkString(string $str): iterable
    {
        $i = 0;
        $length = strlen($str);

        while ($i < $length) {
            $index = $i;

            $ord0 = ord($str[$i++]);

            if ($ord0 < 0x80) {
                yield $index => [
                    $ord0,
                    [$ord0]
                ];
                continue;
            }

            if ($i === $length || $ord0 < 0xC2 || $ord0 > 0xF4) {
                throw new InvalidStringException($str, $i - 1);
            }

            $ord1 = ord($str[$i++]);

            if ($ord0 < 0xE0) {
                if ($ord1 < 0x80 || $ord1 >= 0xC0) {
                    throw new InvalidStringException($str, $i - 1);
                }

                yield $index => [
                    ($ord0 - 0xC0) * 64 + $ord1 - 0x80,
                    [$ord0, $ord1]
                ];

                continue;
            }

            if ($i === $length) {
                throw new InvalidStringException($str, $i - 1);
            }

            $ord2 = ord($str[$i++]);

            if ($ord0 < 0xF0) {
                if ($ord0 === 0xE0) {
                    if ($ord1 < 0xA0 || $ord1 >= 0xC0) {
                        throw new InvalidStringException($str, $i - 2);
                    }
                } elseif ($ord0 === 0xED) {
                    if ($ord1 < 0x80 || $ord1 >= 0xA0) {
                        throw new InvalidStringException($str, $i - 2);
                    }
                } elseif ($ord1 < 0x80 || $ord1 >= 0xC0) {
                    throw new InvalidStringException($str, $i - 2);
                }

                if ($ord2 < 0x80 || $ord2 >= 0xC0) {
                    throw new InvalidStringException($str, $i - 1);
                }

                yield $index => [
                    ($ord0 - 0xE0) * 0x1000 + ($ord1 - 0x80) * 64 + $ord2 - 0x80,
                    [$ord0, $ord1, $ord2]
                ];

                continue;
            }

            if ($i === $length) {
                throw new InvalidStringException($str, $i - 1);
            }

            $ord3 = ord($str[$i++]);

            if ($ord0 < 0xF5) {
                if ($ord0 === 0xF0) {
                    if ($ord1 < 0x90 || $ord1 >= 0xC0) {
                        throw new InvalidStringException($str, $i - 3);
                    }
                } elseif ($ord0 === 0xF4) {
                    if ($ord1 < 0x80 || $ord1 >= 0x90) {
                        throw new InvalidStringException($str, $i - 3);
                    }
                } elseif ($ord1 < 0x80 || $ord1 >= 0xC0) {
                    throw new InvalidStringException($str, $i - 3);
                }

                if ($ord2 < 0x80 || $ord2 >= 0xC0) {
                    throw new InvalidStringException($str, $i - 2);
                }

                if ($ord3 < 0x80 || $ord3 >= 0xC0) {
                    throw new InvalidStringException($str, $i - 1);
                }

                yield $index => [
                    ($ord0 - 0xF0) * 0x40000 + ($ord1 - 0x80) * 0x1000 + ($ord2 - 0x80) * 64 + $ord3 - 0x80,
                    [$ord0, $ord1, $ord2, $ord3]
                ];

                continue;
            }

            throw new InvalidStringException($str, $i - 1);
        }
    }
}