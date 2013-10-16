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

	/** @var string */
	private $pattern;

	/** @var integer */
	private $pos = 0;

	/** @var array */
	private $result = array(), $result_buffer = array();

	/** @var string */
	public $dot_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-';

	/**
	 * Expands the pattern.
	 *
	 * @param string $pattern Pattern
	 *
	 * @return array Expanded pattern
	 */
	public function expand($pattern)
	{
		$escape = false;
		$disjunction = false;
		$disjunct_chars = array();
		$parantheses = false;
		$buffer = '';

		while ($this->pos < strlen($pattern)) {
			$char = $this->charAt($pattern, $this->pos);
			$next_char = strlen($pattern) > $this->pos ? $this->charAt($pattern, $this->pos + 1) : '';

			if (false === $escape && '\\' === $char) {
				$escape = $this->pos;
			} else if (false === $escape && '(' === $char) {
				$parantheses = true;
			} else if (false === $escape && $parantheses && ')' === $char) {
				$bufferExpansion = new ExpExp();
				$expanded_buffer = $bufferExpansion->expand($buffer);
				if ('?' === $next_char) {
					$expanded_buffer[] = '';
					$this->pos++;
				}
				$this->result = $this->addToAll($this->result, $expanded_buffer);
				$parantheses = false;
				$buffer = '';
			} else if ($parantheses) {
				$buffer = $buffer . $char;
			} else if (false === $escape && '[' === $char) {
				$disjunction = true;
			} else if (false === $escape && $disjunction && ']' === $char) {
				$this->result = $this->addToAll($this->result, $disjunct_chars);
				$disjunction = false;
				$disjunct_chars = array();
			} else if ($disjunction) {
				$disjunct_chars[] = $char;
			} else if (false === $escape && '.' === $char) {
				$this->result = $this->addToAll($this->result, str_split($this->dot_chars, 1));
			} else if (false === $escape && '|' === $char) {
				$this->result_buffer[] = $this->result;
				$this->result = array();
			} else {
				if ('?' === $next_char) {
					$this->result = $this->addToAll($this->result, array($char, ''));
					$this->pos++;
				} else {
					$this->result = $this->addChar($this->result, $char);
				}
			}

			// $escape contains the position when the escape occured, if we passed the escaped characters, reset the flag.
			if ($this->pos > $escape) {
				$escape = false;
			}

			$this->pos++;
		}

		if (count($this->result_buffer) > 0) {
			$result = array();
			foreach ($this->result_buffer as $item) {
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
	 * @return string Character
	 */
	private function charAt($string, $position)
	{
		return substr($string, $position, 1);
	}

	private function addChar($array, $char)
	{
		$size = count($array);
		if (0 === $size) {
			return array($char);
		}
		for ($i = 0; $i < $size; $i++) {
			$array[$i] = $array[$i] . $char;
		}
		return $array;
	}

	private function addToAll($array, $chars)
	{
		$new_array = array();
		$size = count($array);

		if (0 === $size) {
			$array = array('');
		}

		for ($i = 0; $i < count($array); $i++) {
			for ($j = 0; $j < count($chars); $j++) {
				$new_array[] = $array[$i] . $chars[$j];
			}
		}

		return $new_array;
	}

}
