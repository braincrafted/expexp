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

use Braincrafted\ExpExp\ExpExp;

/**
 * ExpExpTest
 *
 * @category  Test
 * @package   braincrafted/expexp
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2011-2013 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @group     unit
 */
class ExpExpTest extends \PHPUnit_Framework_TestCase
{
    /** @var ExpExp */
    private $exp;

    public function setUp()
    {
        $this->exp = new ExpExp;
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::addToAll()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandSimpleDisjunction()
    {
    	$result = $this->exp->expand('[abc]xyz[abc]');

    	$this->assertCount(9, $result);
    	$this->assertContains('axyza', $result);
    	$this->assertContains('axyzb', $result);
    	$this->assertContains('axyzc', $result);
    	$this->assertContains('bxyza', $result);
    	$this->assertContains('bxyzb', $result);
    	$this->assertContains('bxyzc', $result);
    	$this->assertContains('cxyza', $result);
    	$this->assertContains('cxyzb', $result);
    	$this->assertContains('cxyzc', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::addToAll()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandEscapedDisjunction()
    {
    	$result = $this->exp->expand('\[abc\]xyz\[abc\]');
    	$this->assertCount(1, $result);
    	$this->assertContains('[abc]xyz[abc]', $result);
    }

    public function testExpandMultiplication()
    {
        $result = $this->exp->expand('a{3}');
        $this->assertCount(1, $result);
        $this->assertContains('aaa', $result);
    }

    public function testExpandEmptyMultiplication()
    {
        $result = $this->exp->expand('a{}');
        $this->assertCount(1, $result);
        $this->assertContains('a', $result);
    }

    public function testExpandMinMaxMultiplication()
    {
        $result = $this->exp->expand('a{1,3}');
        $this->assertCount(3, $result);
        $this->assertContains('a', $result);
        $this->assertContains('aa', $result);
        $this->assertContains('aaa', $result);
    }

    public function testExpandMaxMultiplication()
    {
        $result = $this->exp->expand('a{,3}');
        $this->assertCount(4, $result);
        $this->assertContains('', $result);
        $this->assertContains('a', $result);
        $this->assertContains('aa', $result);
        $this->assertContains('aaa', $result);
    }

    public function testExpandParanthesesMultiplication()
    {
        $result = $this->exp->expand('a(bc){2}');
        $this->assertCount(1, $result);
        $this->assertContains('abcbc', $result);
    }

    public function testExpandParanthesesMinMaxMultiplication()
    {
        $result = $this->exp->expand('a(bc){1,2}');
        $this->assertCount(2, $result);
        $this->assertContains('abc', $result);
        $this->assertContains('abcbc', $result);
    }


    public function testExpandParanthesesMaxMultiplication()
    {
        $result = $this->exp->expand('a(bc){,2}');
        $this->assertCount(3, $result);
        $this->assertContains('a', $result);
        $this->assertContains('abc', $result);
        $this->assertContains('abcbc', $result);
    }

    public function testExpandDisjunctionMutliplication()
    {
        $result = $this->exp->expand('[ab]{2}');
        $this->assertCount(2, $result);
        $this->assertContains('aa', $result);
        $this->assertContains('bb', $result);
    }

    public function testExpandDisjunctionMinMaxMutliplication()
    {
        $result = $this->exp->expand('[ab]{0,2}');
        $this->assertCount(6, $result);
        $this->assertContains('', $result);
        $this->assertContains('a', $result);
        $this->assertContains('b', $result);
        $this->assertContains('aa', $result);
        $this->assertContains('bb', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testEscapedEscaped()
    {
       	$result = $this->exp->expand('\\\abc');
    	$this->assertCount(1, $result);
    	$this->assertContains('\abc', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::addToAll()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandDotOperator()
    {
    	$result = $this->exp->expand('ab.');
    	$this->assertCount(63, $result);
    	$this->assertContains('abA', $result);
    	$this->assertContains('aba', $result);
    	$this->assertContains('ab0', $result);
    	$this->assertContains('ab-', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandEscapedDotOperator()
    {
    	$result = $this->exp->expand('ab\.');
    	$this->assertCount(1, $result);
    	$this->assertContains('ab.', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::addToAll()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
	public function testExpandParantheses()
	{
		$result = $this->exp->expand('ab(c)');
		$this->assertCount(1, $result);
		$this->assertContains('abc', $result);
	}

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandAlternation()
    {
		$result = $this->exp->expand('abc|xyz');
		$this->assertCount(2, $result);
		$this->assertContains('abc', $result);
		$this->assertContains('xyz', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::addToAll()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandAlternationInParantheses()
    {
		$result = $this->exp->expand('ab(c|d)');
		$this->assertCount(2, $result);
		$this->assertContains('abc', $result);
		$this->assertContains('abd', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::addToAll()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandAlternationDisjunctionInParantheses()
    {
		$result = $this->exp->expand('ab(cde|[xyz])');
		$this->assertCount(4, $result);
		$this->assertContains('abcde', $result);
		$this->assertContains('abx', $result);
		$this->assertContains('aby', $result);
		$this->assertContains('abz', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandOptional()
    {
    	$result = $this->exp->expand('abc?');
    	$this->assertCount(2, $result);
    	$this->assertContains('abc', $result);
    	$this->assertContains('ab', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::addToAll()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandOptionalParantheses()
    {
    	$result = $this->exp->expand('abc(xyz)?');
    	$this->assertCount(2, $result);
    	$this->assertContains('abc', $result);
    	$this->assertContains('abcxyz', $result);
    }

    /**
     * @covers Braincrafted\ExpExp\ExpExp::expand()
     * @covers Braincrafted\ExpExp\ExpExp::charAt()
     * @covers Braincrafted\ExpExp\ExpExp::addChar()
     */
    public function testExpandEscapedOptional()
    {
        $result = $this->exp->expand('abc\?');
        $this->assertCount(1, $result);
        $this->assertContains('abc?', $result);
    }
}
