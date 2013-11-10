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
    public $dotChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_';

    /** @var array */
    private $classes = [
        'digit'    => '0123456789',
        'lower' => 'abcdefghijklmnopqrstuvwxyz',
        'upper'    => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'word'  => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_',
        'space' => "\t\n\r ",
        'vspace' => "\n\r",
        'hspace' => "\t ",
        'punct' => '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'
    ];

    /** @var integer */
    private $pos;

    /** @var array */
    private $result;

    /** @var array */
    private $alternateResults;

    /** @var false|integer */
    private $escape;

    /**
     * Expands the pattern.
     *
     * @param string $pattern  Pattern.
     * @param string $stopChar Character on which the expansion should stop.
     *
     * @return array Expanded pattern.
     */
    public function expand($pattern, $stopChar = null)
    {
        $this->pos = 0;
        $this->result = [ '' ];
        $this->escape = false;
        $this->alternateResults = [ ];

        while ($this->pos < strlen($pattern)) {
            $char = substr($pattern, $this->pos, 1);

            if (null !== $stopChar && $char === $stopChar) {
                $this->pos += 1;
                break;
            }

            if (false === $this->escape) {
                $this->expandCharacter($pattern);
            } else {
                $this->expandEscapedCharacter($char);
            }

            if ($this->pos > $this->escape) {
                $this->escape = false;
            }
            $this->pos += 1;
        }

        $this->mergeResults($this->alternateResults);

        return $this->result;
    }

    /**
     * Returns the current position of the expansion.
     *
     * @return integer Current position of the expansion.
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * Expands the given character.
     *
     * @param string $pattern Pattern.
     */
    protected function expandCharacter($pattern)
    {
        $char = substr($pattern, $this->pos, 1);
        $nextChar = substr($pattern, $this->pos+1, 1);

        if ('\\' === $char) {
            $this->escape = $this->pos;
        } elseif ('{' === $nextChar) {
            $this->pos += 1;
            $buffer = $this->repeat($pattern, $char);
            $this->addAll($buffer);
        } elseif ('(' === $char) {
            $this->expandParentheses($pattern);
        } elseif ('[' === $char && ':' !== $nextChar) {
            $this->expandDisjunction($pattern);
        } elseif ('?' === $nextChar) {
            $this->pos += 1;
            $this->addAll([ $char, '' ]);
        } elseif ('.' === $char) {
            $this->addAll(str_split($this->dotChars, 1));
        } elseif ('|' === $char) {
            $this->alternateResults[] = $this->result;
            $this->result = [ '' ];
        } elseif (':' === $char && ']' == $nextChar) {
            $this->pos += 1;
            $this->add(':]');
        } else {
            $this->add($char);
        }
    }

    /**
     * Expands the given escaped character.
     *
     * @param string $char Current character.
     */
    protected function expandEscapedCharacter($char)
    {
        if ('d' === $char) {
            $this->addAll(str_split($this->getClass('digit'), 1));
        } elseif ('w' === $char) {
            $this->addAll(str_split($this->getClass('word'), 1));
        } elseif ('s' === $char) {
            $this->addAll(str_split($this->getClass('space'), 1));
        } elseif ('v' === $char) {
            $this->addAll(str_split($this->getClass('vspace'), 1));
        } elseif ('h' === $char) {
            $this->addAll(str_split($this->getClass('hspace'), 1));
        } else {
            $this->add($char);
        }
    }

    /**
     * Expands the content of parentheses.
     *
     * @param string $pattern Pattern.
     *
     * @return void
     */
    protected function expandParentheses($pattern)
    {
        $bufferExp = new ExpExp;
        $charBuffer = $bufferExp->expand(substr($pattern, $this->pos+1), ')');
        $this->pos += $bufferExp->getPos();
        if ('?' === substr($pattern, $this->pos+1, 1)) {
            $this->pos += 1;
            $charBuffer[] = '';
        } elseif ('{' === substr($pattern, $this->pos+1, 1)) {
            $this->pos += 1;
            $charBuffer = $this->repeat($pattern, $charBuffer);
        }
        $this->addAll($charBuffer);
    }

    /**
     * Expands a disjunction.
     *
     * @param string $pattern Pattern.
     *
     * @return void
     */
    protected function expandDisjunction($pattern)
    {
        $bufferExp = new ExpExp;
        $buffer = $bufferExp->expand(substr($pattern, $this->pos+1), ']');
        $buffer[0] = preg_replace_callback(
            '/(\[:([a-z]+):\])/',
            function ($matches) {
                return $this->getClass($matches[2]);
            },
            $buffer[0]
        );
        $buffer = str_split($buffer[0], 1);
        $this->pos += $bufferExp->getPos();
        if ('{' === substr($pattern, $this->pos+1, 1)) {
            $this->pos += 1;
            $buffer = $this->repeat($pattern, $buffer);
        }
        $this->addAll($buffer);
    }

    /**
     * Merges the results from the given alternatives with the result.
     *
     * @param array $alternates List of alternate results.
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
     * @param string $string String to add to the result.
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
     * @param array $strings Array of strings to add to the result.
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
     * @param string $pattern Pattern.
     * @param string $add     String to repeat.
     *
     * @return array Result of the repetition.
     */
    protected function repeat($pattern, $add)
    {
        if (false === is_array($add)) {
            $add = [ $add ];
        }

        $bufferExp = new ExpExp;
        $buffer = $bufferExp->expand(substr($pattern, $this->pos+1), '}')[0];
        list($min, $max) = $this->parseRepetition($buffer);

        $this->pos += $bufferExp->getPos();

        $buffer = [];
        for ($i = $min; $i <= $max; $i++) {
            foreach ($add as $el) {
                $buffer[] = str_repeat($el, $i);
            }
        }

        return $buffer;
    }

    /**
     * Parses the min and max values from the given repetition pattern.
     *
     * @param string $string Repetition pattern.
     *
     * @return array Array with two values, min and max.
     */
    protected function parseRepetition($string)
    {
        if (0 === strlen($string)) {
            return [ 0, 0 ];
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

    /**
     * Returns the character class(es) with the given name(s). Multiple classes can be connected by +.
     *
     * @param string $name Character class or list of character classes.
     *
     * @return string List of characters from the given class(es).
     *
     * @throws \InvalidArgumentException if the character class does not exist.
     */
    protected function getClass($name)
    {
        if (false === isset($this->classes[$name])) {
            throw new \InvalidArgumentException(sprintf('The character class %s does not exist.', $name));
        }
        return $this->classes[$name];
    }
}
