<?php

namespace Ninjawhois\RegExpExpansion;

class RegExpExpansion
{

	/** @var string */
	private $pattern;

	/** @var integer */
	private $pos = 0;

	/** @var array */
	private $result = array(), $result_buffer = array();

	/** @var string */
	public $dot_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-';

	public function __construct($pattern)
	{
		$this->pattern = $pattern;
	}

	public function expand()
	{
		$escape = false;
		$disjunction = false;
		$disjunct_chars = array();
		$parantheses = false;
		$buffer = '';

		while ($this->pos < strlen($this->pattern)) {
			$char = $this->charAt($this->pattern, $this->pos);
			$next_char = strlen($this->pattern) > $this->pos ? $this->charAt($this->pattern, $this->pos + 1) : '';
			if (false === $escape && '\\' === $char) {
				$escape = $this->pos;
			}
			elseif (false === $escape && '(' === $char) {
				$parantheses = true;
			}
			elseif (false === $escape && $parantheses && ')' === $char) {
				$bufferExpansion = new RegExpExpansion($buffer);
				$expanded_buffer = $bufferExpansion->expand();
				if ('?' === $next_char) {
					$expanded_buffer[] = '';
					$this->pos++;
				}
				$this->result = $this->addToAll($this->result, $expanded_buffer);
				$parantheses = false;
				$buffer = '';
			}
			elseif ($parantheses) {
				$buffer = $buffer . $char;
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
			elseif (false === $escape && '.' === $char) {
				$this->result = $this->addToAll($this->result, str_split($this->dot_chars, 1));
			}
			elseif (false === $escape && '|' === $char) {
				$this->result_buffer[] = $this->result;
				$this->result = array();
			}
			else {
				if ('?' === $next_char) {
					$this->result = $this->addToAll($this->result, array($char, ''));
					$this->pos++;
				}
				else {
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
