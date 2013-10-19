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
	/** @var string */
	public $dotChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-';

	/** @var integer */
	private $pos;

	/** @var array */
	private $result;

	/**
	 * Expands the pattern.
	 *
	 * @param string $pattern  Pattern
	 * @param string $stopChar Character on which the expansion should stop
	 *
	 * @return array Expanded pattern
	 */
	public function expand($pattern, $stopChar = null)
	{
		$this->pos = 0;
		$this->result = [ '' ];
		$escape = false;
		$alternateResults = [ ];

		while ($this->pos < strlen($pattern)) {
			$char = substr($pattern, $this->pos, 1);
			$nextChar = substr($pattern, $this->pos+1, 1);

			if (null !== $stopChar && $char === $stopChar) {
				$this->pos += 1;
				break;
			}

			if (false === $escape && '\\' === $char) {
				$escape = $this->pos;
			} else if (false === $escape && '{' === $nextChar) {
				$this->pos += 1;
				$buffer = $this->repeat($pattern, $char);
				$this->addAll($buffer);
			} else if (false === $escape && '(' === $char) {
				$bufferExp = new ExpExp;
				$charBuffer = $bufferExp->expand(substr($pattern, $this->pos+1), ')');
				$this->pos += $bufferExp->getPos();
				if ('?' === substr($pattern, $this->pos+1, 1)) {
					$this->pos += 1;
					$charBuffer[] = '';
				} else if ('{' === substr($pattern, $this->pos+1, 1)) {
					$this->pos += 1;
					$charBuffer = $this->repeat($pattern, $charBuffer);
				}
				$this->addAll($charBuffer);
			} else if (false === $escape && '[' === $char) {
				$bufferExp = new ExpExp;
				$buffer = $bufferExp->expand(substr($pattern, $this->pos+1), ']');
				$buffer = str_split($buffer[0], 1);
				$this->pos += $bufferExp->getPos();
				if ('{' === substr($pattern, $this->pos+1, 1)) {
					$this->pos += 1;
					$buffer = $this->repeat($pattern, $buffer);
				}
				$this->addAll($buffer);
			} else if (false === $escape && '?' === $nextChar) {
				$this->pos += 1;
				$this->addAll([ $char, '' ]);
			} else if (false === $escape && '.' === $char) {
				$this->addAll(str_split($this->dotChars, 1));
			} else if (false === $escape && '|' === $char) {
				$alternateResults[] = $this->result;
				$this->result = [ '' ];
			} else {
				$this->add($char);
			}

			if ($this->pos > $escape) {
				$escape = false;
			}
			$this->pos += 1;
		}

		$this->mergeResults($alternateResults);

		return $this->result;
	}

	/**
	 * Returns the current position of the expansion
	 *
	 * @return integer Current position of the expansion
	 */
	public function getPos()
	{
	    return $this->pos;
	}

	/**
	 * Merges the results from the given alternatives with the result.
	 *
	 * @param array $alternates List of alternate results
	 */
	protected function mergeResults($alternates)
	{
		$buffer = [];

	    foreach ($alternates as $alternate) {
	    	$buffer = array_merge($buffer, $alternate);
	    }

	    $this->result = array_merge($buffer, $this->result);
	}

	/**
	 * Adds the given string to every element in the result.
	 *
	 * @param string $string String to add to the result
	 */
	protected function add($string)
	{
		$buffer = [];

	    for ($i = 0; $i < count($this->result); $i++) {
	    	$buffer[] = $this->result[$i].$string;
	    }

	    $this->result = $buffer;
	}

	/**
	 * Adds all given strings to every element in the result.
	 *
	 * @param array $strings Array of strings to add to the result
	 */
	protected function addAll(array $strings)
	{
	    $buffer = [];

	    for ($i = 0; $i < count($this->result); $i++) {
	    	for ($j = 0; $j < count($strings); $j++) {
	    		$buffer[] = $this->result[$i].$strings[$j];
	    	}
	    }

	    $this->result = $buffer;
	}

	/**
	 * Repeats the given string depending on the given pattern.
	 *
	 * @param string $pattern Pattern
	 * @param string $add     String to repeat
	 *
	 * @return array Result of the repetition
	 */
	protected function repeat($pattern, $add)
	{
		$bufferExp = new ExpExp;
		$buffer = $bufferExp->expand(substr($pattern, $this->pos+1), '}')[0];
		list($min, $max) = $this->parseRepetition($buffer);
		$this->pos += $bufferExp->getPos();
		$buffer = [];
		for ($i = $min; $i <= $max; $i++) {
			if (true === is_array($add)) {
				foreach ($add as $el) {
					$buffer[] = str_repeat($el, $i);
				}
			} else {
				$buffer[] = str_repeat($add, $i);
			}
		}

		return $buffer;
	}

	/**
	 * Parses the min and max values from the given repetition pattern.
	 *
	 * @param string $string Repetition pattern
	 *
	 * @return array Array with two values, min and max
	 */
	protected function parseRepetition($string)
	{
		if (0 === strlen($string)) {
			return [ 1, 1 ];
		}
	    if (false === strpos($string, ',')) {
	    	return [ $string, $string ];
	    }

	    list($min, $max) = explode(',', $string);
	    if (!$min) {
	    	$min = 0;
	    }

	    return [ $min, $max ];
	}
}
