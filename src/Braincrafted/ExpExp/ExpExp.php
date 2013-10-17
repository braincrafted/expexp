<?php
/**
 * This file is part of braincrafted/expexp.
 *
 * (c) 2011-2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\ExpExp;

/**
 * ExpExp
 *
 * @package   braincrafted/expexp
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2011-2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 */
class ExpExp
{
	/** @var array */
	private $result = array(), $resultBuffer = array();

	/** @var string */
	public $dotChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-';

	/**
	 * Expands the pattern.
	 *
	 * @param string $pattern Pattern
	 *
	 * @return array Expanded pattern
	 */
	public function expand($pattern)
	{
		$result = array();
		$resultBuffer = array();
		$pos = 0;
		$escape = false;
		$disjunction = false;
		$disjunctChars = array();
		$parentheses = false;
		$multiplication = false;
		$buffer = '';
		$multiplier = '';

		while ($pos < strlen($pattern)) {
			$char = $this->charAt($pattern, $pos);
			$nextChar = strlen($pattern) > $pos ? $this->charAt($pattern, $pos + 1) : '';

			if (false === $escape && '\\' === $char) {
				$escape = $pos;
			} else if (false === $escape && '(' === $char) {
				$parentheses = true;
			} else if (false === $escape && true === $disjunction && ']' === $char && '{' === $nextChar) {
				$multiplication = true;
			} else if (false === $escape && true === $disjunction && true === $multiplication && '}' === $char) {
				$multiplier = $this->parseMultiplier($multiplier);
				$newBuffer = array();
				for ($i = 0; $i < count($disjunctChars); $i++) {
					$newBuffer = array_merge($newBuffer, $this->multiply($disjunctChars[$i], $multiplier));
				}
				$result = $this->addToAll($result, $newBuffer);
				$disjunction = false;
				$multiplication = false;
			} else if (false === $escape && true === $parentheses && true === $multiplication && '}' === $char) {
				$multiplier = $this->parseMultiplier($multiplier);
				$newBuffer = array();
				for ($i = 0; $i < count($expandedBuffer); $i++) {
					$newBuffer = array_merge($newBuffer, $this->multiply($expandedBuffer[$i], $multiplier));
				}
				$result = $this->addToAll($result, $newBuffer);
				$parantheses = false;
				$multiplication = false;
			} else if (false === $escape && true === $multiplication && '}' === $char) {
				$multiplier = $this->parseMultiplier($multiplier);
				$bufferResult = $this->multiply($buffer, $multiplier);
				$result = $this->addToAll($result, $bufferResult);
				$multiplication = false;
			} else if (true === $multiplication && '{' !== $char) {
				$multiplier = $multiplier.$char;
			} else if (true === $multiplication) {
			} else if (false === $escape && true === $parentheses && ')' === $char) {
				// An open parentheses is closed, expand the pattern inside
				$bufferExpansion = new ExpExp();
				$expandedBuffer = $bufferExpansion->expand($buffer);
				if ('?' === $nextChar) {
					$expandedBuffer[] = '';
					$pos++;
				}
				if ('{' !== $nextChar) {
					$result = $this->addToAll($result, $expandedBuffer);
					$parentheses = false;
				} else {
					$multiplication = true;
				}
				$buffer = '';
			} else if (true === $parentheses) {
				// Inside a parentheses
				$buffer = $buffer . $char;
			} else if (false === $escape && '{' === $nextChar) {
				$multiplication = true;
				$buffer = $char;
			} else if (false === $escape && '[' === $char) {
				// A disjunction section is about to start
				$disjunction = true;
			} else if (false === $escape && true === $disjunction && ']' === $char) {
				// A disjunction is ending
				$result = $this->addToAll($result, $disjunctChars);
				$disjunction = false;
				$disjunctChars = array();
			} else if (true === $disjunction) {
				$disjunctChars[] = $char;
			} else if (false === $escape && '.' === $char) {
				$result = $this->addToAll($result, str_split($this->dotChars, 1));
			} else if (false === $escape && '|' === $char) {
				$resultBuffer[] = $result;
				$result = array();
			} else {
				if ('?' === $nextChar) {
					$result = $this->addToAll($result, array($char, ''));
					$pos++;
				} else {
					$result = $this->addChar($result, $char);
				}
			}

			// $escape contains the position when the escape occured, if we passed the escaped characters, reset the flag.
			if ($pos > $escape) {
				$escape = false;
			}

			$pos++;
		}

		if (count($resultBuffer) > 0) {
			$newResult = array();
			foreach ($resultBuffer as $item) {
				$newResult = array_merge($newResult, $item);
			}
			$result = array_merge($newResult, $result);
		}

		return $result;
	}

	/**
	 * Returns the character at the given position.
	 *
	 * @param string  $string   String
	 * @param integer $position Position
	 *
	 * @return string Character
	 */
	private function charAt($string, $position)
	{
		return substr($string, $position, 1);
	}

	/**
	 * Adds $char to each element in $array.
	 *
	 * @param array  $array Array
	 * @param string $char  Character
	 *
	 * @return array Array with char added
	 */
	private function addChar(array $array, $char)
	{
		if (0 === count($array)) {
			return array($char);
		}

		return array_map(
			function ($x) use ($char) {
				return $x.$char;
			},
			$array
		);
	}

	/**
	 * Adds each character from $chars to every element from $array.
	 *
	 * For example, $array = ['a','b'] and $chars = ['x','y'] would produce
	 * - ax
	 * - ay
	 * - bx
	 * - by
	 *
	 * @param array $array Array of strings
	 * @param array $chars Array of chars
	 *
	 * @return array Array with chars added to strings
	 */
	private function addToAll(array $array, array $chars)
	{
		$newArray = array();

		if (0 === count($array)) {
			$array = array('');
		}

		for ($i = 0; $i < count($array); $i++) {
			for ($j = 0; $j < count($chars); $j++) {
				$newArray[] = $array[$i] . $chars[$j];
			}
		}

		return $newArray;
	}

	private function parseMultiplier($multiplier)
	{
	    if (0 === strlen($multiplier)) {
	    	return array(1, 1);
	    }
	    if (false === strpos($multiplier, ',')) {
	    	return array($multiplier, $multiplier);
	    }
	    list($min, $max) = explode(',', $multiplier);
	    if (0 === strlen($min)) {
	    	$min = 0;
	    }
	    if (0 === strlen($max)) {
	    }

	    return array($min, $max);
	}

	private function multiply($string, array $multiplier)
	{
		$result = array();
	    for ($i = $multiplier[0]; $i <= $multiplier[1]; $i++) {
	    	$result[] = str_repeat($string, $i);
	    }

	    return $result;
	}
}
