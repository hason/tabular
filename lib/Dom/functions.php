<?php

/*
 * This file is part of the Tabular  package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This file contains functions which are used in XPath expressions.
 * Note that attempting to use a static class for this purpose does not work, it
 * would seem that only functions can be registered in an XPath instance.
 */
namespace PhpBench\Tabular\Dom\functions;

/**
 * Return the sum of all the given values.
 *
 * @param array $values
 *
 * @return mixed
 */
function sum($values)
{
    $sum = 0;
    foreach (getValues($values) as $value) {
        $sum += $value;
    }

    return $sum;
}

/**
 * Return the lowest value contained within the given values.
 *
 * @param array $values
 *
 * @return mixed
 */
function min($values)
{
    $min = null;
    foreach (getValues($values) as $value) {
        if (null === $min || $value < $min) {
            $min = $value;
        }
    }

    return $min;
}

/**
 * Return the highest value contained within the given values.
 *
 * @param array $values
 *
 * @return mixed
 */
function max($values)
{
    $max = null;
    foreach (getValues($values) as $value) {
        if (null === $max || $value > $max) {
            $max = $value;
        }
    }

    return $max;
}

/**
 * Return the mean (average) value of the given values.
 *
 * @param array $values
 *
 * @return mixed
 */
function mean($values)
{
    if (empty($values)) {
        return 0;
    }

    $values = getValues($values);

    $sum = sum($values);

    if (0 == $sum) {
        return 0;
    }

    $count = count($values);

    return $sum / $count;
}

/**
 * Return the median value of the given values.
 *
 * @param array $values
 *
 * @return mixed
 */
function median($values)
{
    if (empty($values)) {
        return 0;
    }

    $values = getValues($values);

    sort($values);
    $nbValues = count($values);
    $middleIndex = $nbValues / 2;

    if (count($values) % 2 == 1) {
        return $values[ceil($middleIndex) - 1];
    }

    return ($values[$middleIndex - 1] + $values[$middleIndex]) / 2;
}

/**
 * Return the deviation as a percentage from the given value.
 *
 * @param mixed $standardValue
 * @param mixed $actualValue
 *
 * @return int
 */
function deviation($standardValue, $actualValue)
{
    $actualValue = getValue($actualValue);

    if (0 == $standardValue) {
        return $actualValue;
    }

    if (!is_numeric($standardValue) || !is_numeric($actualValue)) {
        throw new \RuntimeException(
            'Deviation must be passed numeric values.'
        );
    }

    return 100 / $standardValue * ($actualValue - $standardValue);
}

function abs($value)
{
    return \abs($value);
}

/**
 * Return the standard deviation of a given population
 *
 * @param \DOMNodeList $nodeList
 * @param boolean $sample
 *
 * @return float
 */
function stdev($values, $sample = false)
{
    $variance = variance($values, $sample);

    return sqrt($variance);
}

/**
 * Return the variance for a given population.
 *
 * @param array $values
 * @param boolean $sample
 *
 * @return float
 */
function variance($values, $sample = false)
{
    $average = mean($values);
    $values = getValues($values);
    $sum = 0;
    foreach ($values as $value) {
        $diff = pow($value - $average, 2);
        $sum += $diff;
    }
    $variance = $sum / (count($values) - ($sample ? 1 : 0));

    return $variance;
}

/**
 * Convert a DOMNodeList into an array.
 */
function getValues($values)
{
    $newValues = array();
    foreach ($values as $value) {
        if ($value instanceof \DOMNode) {
            $value = $value->nodeValue;
        }

        if (!is_scalar($value)) {
            throw new \InvalidArgumentException(sprintf('Values passed as an array must be scalar, got "%s"',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        $newValues[] = $value;
    }

    return $newValues;
}

/**
 * Return a single value from a DOMNodeList.
 */
function getValue($value)
{
    $values = getValues(array($value));

    return reset($values);
}
