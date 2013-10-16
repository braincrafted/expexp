<?php
/**
 * This file is part of BcExpExp.
 *
 * (c) 2011-2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bc\ExpExp;

/**
 * ExpExp
 *
 * @package   BcExpExp
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
		$pos = 0;
		$escape = false;
		$disjunction = false;
		$disjunctCchars = array();
		$parentheses = false;
		$buffer = '';

		while ($pos < strlen($pattern)) {
			$char = $this->charAt($pattern, $pos);
			$nextChar = strlen($pattern) > $pos ? $this->charAt($pattern, $pos + 1) : '';

			if (false === $escape && '\\' === $char) {
				$escape = $pos;
			} else if (false === $escape && '(' === $char) {
				$parentheses = true;
			} else if (false === $escape && true === $parentheses && ')' === $char) {
				// An open parentheses is closed, expand the pattern inside
				$bufferExpansion = new ExpExp();
				$expanded_buffer = $bufferExpansion->expand($buffer);
				if ('?' === $nextChar) {
					$expanded_buffer[] = '';
					$pos++;
				}
				$this->result = $this->addToAll($this->result, $expanded_buffer);
				$parentheses = false;
				$buffer = '';
			} else if (true === $parentheses) {
				// Inside a parentheses
				$buffer = $buffer . $char;
			} else if (false === $escape && '[' === $char) {
				// A disjunction section is about to start
				$disjunction = true;
			} else if (false === $escape && true === $disjunction && ']' === $char) {
				// A disjunction is ending
				$this->result = $this->addToAll($this->result, $disjunctCchars);
				$disjunction = false;
				$disjunctCchars = array();
			} else if (true === $disjunction) {
				$disjunctCchars[] = $char;
			} else if (false === $escape && '.' === $char) {
				$this->result = $this->addToAll($this->result, str_split($this->dotChars, 1));
			} else if (false === $escape && '|' === $char) {
				$this->resultBuffer[] = $this->result;
				$this->result = array();
			} else {
				if ('?' === $nextChar) {
					$this->result = $this->addToAll($this->result, array($char, ''));
					$pos++;
				} else {
					$this->result = $this->addChar($this->result, $char);
				}
			}

			// $escape contains the position when the escape occured, if we passed the escaped characters, reset the flag.
			if ($pos > $escape) {
				$escape = false;
			}

			$pos++;
		}

		if (count($this->resultBuffer) > 0) {
			$result = array();
			foreach ($this->resultBuffer as $item) {
				$result = array_merge($result, $item);
			}
			$this->result = array_merge($result, $this->result);
		}

		return $this->result;
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
	 * Adds all characters from $chars to all elements from $array.
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

}
