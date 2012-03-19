<?php

namespace Ninjawhois\RegExpExpansion;

class RegExpExpansion
{

	/** @var string */
	private $pattern;

	/** @var integer */
	private $pos = 0;

	/** @var array */
	private $result = array();

	public function __construct($pattern)
	{
		$this->pattern = $pattern;
	}

	public function expand()
	{
		$escape = false;
		$disjunction = false;
		$disjunct_chars = array();

		while ($this->pos < strlen($this->pattern)) {
			$char = $this->charAt($this->pattern, $this->pos);
			if (false === $escape && '\\' === $char) {
				$escape = $this->pos;
			}
			elseif (false === $escape && '[' === $char) {
				$disjunction = true;
			}
			elseif (false === $escape && $disjunction && ']' === $char) {
				$this->result = $this->addToAll($this->result, $disjunct_chars);
				$disjunction = false;
				$disjunct_chars = array();
			}
			elseif ($disjunction) {
				$disjunct_chars[] = $char;
			}
			else {
				$this->result = $this->addChar($this->result, $char);
			}

			// $escape contains the position when the escape occured, if we passed the escaped characters, reset the flag.
			if ($this->pos > $escape) {
				$escape = false;
			}

			$this->pos++;
		}

		return $this->result;
	}

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
