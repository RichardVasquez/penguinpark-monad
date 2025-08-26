<?php declare(strict_types=1);

namespace PenguinPark\Monad\Util;

/**
 * Structural/value equality helpers.
 *
 * Compares values with special handling for floats (NaN==NaN) and arrays (pairwise structural equality).
 * For objects/resources/closures, falls back to identity (===).
 */
final class Eq
{
    /**
     * Compare two values for structural equality.
     *
     * Rules:
     * - Floats: NaN equals NaN; other floats compare by strict identity (===).
     * - Arrays: same length and pairwise valueEq on reindexed (array_values) arrays.
     * - Objects/closures/resources: strict identity (===).
     * - All other scalars/mixed: strict identity (===).
     *
     * @param mixed $a
     * @param mixed $b
     * @return bool True if values are considered equal per the rules above
     */
    public static function valueEq(mixed $a, mixed $b): bool
    {
        // Floats: NaN == NaN; PHP already treats +0.0 === -0.0 as true
        if (is_float($a) && is_float($b)) {
            if (is_nan($a) && is_nan($b)) {
                return true;
            }
            return $a === $b;
        }
        // Arrays: structural, order matters
        if (is_array($a) && is_array($b)) {
            if (count($a) !== count($b)) {
                return false;
            }
            $av = array_values($a);
            $bv = array_values($b);
            for ($i = 0, $n = count($av); $i < $n; $i++) {
                if (!self::valueEq($av[$i], $bv[$i])) {
                    return false;
                }
            }
            return true;
        }
        // Objects/closures/resources: identity only by default
        return $a === $b;
        // Scalars/mixed: strict
    }
}