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

use Bc\ExpExp\ExpExp;

/**
 * ExpExpTest
 *
 * @category  Test
 * @package   BcExpExp
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
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::addToAll()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
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
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::addToAll()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
     */
    public function testExpandEscapedDisjunction()
    {
    	$result = $this->exp->expand('\[abc\]xyz\[abc\]');
    	$this->assertCount(1, $result);
    	$this->assertContains('[abc]xyz[abc]', $result);
    }

    /**
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
     */
    public function testEscapedEscaped()
    {
       	$result = $this->exp->expand('\\\abc');
    	$this->assertCount(1, $result);
    	$this->assertContains('\abc', $result);
    }

    /**
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::addToAll()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
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
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
     */
    public function testExpandEscapedDotOperator()
    {
    	$result = $this->exp->expand('ab\.');
    	$this->assertCount(1, $result);
    	$this->assertContains('ab.', $result);
    }

    /**
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::addToAll()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
     */
	public function testExpandParantheses()
	{
		$result = $this->exp->expand('ab(c)');
		$this->assertCount(1, $result);
		$this->assertContains('abc', $result);
	}

    /**
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
     */
    public function testExpandAlternation()
    {
		$result = $this->exp->expand('abc|xyz');
		$this->assertCount(2, $result);
		$this->assertContains('abc', $result);
		$this->assertContains('xyz', $result);
    }

    /**
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::addToAll()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
     */
    public function testExpandAlternationInParantheses()
    {
		$result = $this->exp->expand('ab(c|d)');
		$this->assertCount(2, $result);
		$this->assertContains('abc', $result);
		$this->assertContains('abd', $result);
    }

    /**
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::addToAll()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
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
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
     */
    public function testExpandOptional()
    {
    	$result = $this->exp->expand('abc?');
    	$this->assertCount(2, $result);
    	$this->assertContains('abc', $result);
    	$this->assertContains('ab', $result);
    }

    /**
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::addToAll()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
     */
    public function testExpandOptionalParantheses()
    {
    	$result = $this->exp->expand('abc(xyz)?');
    	$this->assertCount(2, $result);
    	$this->assertContains('abc', $result);
    	$this->assertContains('abcxyz', $result);
    }

    /**
     * @covers Bc\ExpExp\ExpExp::expand()
     * @covers Bc\ExpExp\ExpExp::charAt()
     * @covers Bc\ExpExp\ExpExp::addChar()
     */
    public function testExpandEscapedOptional()
    {
        $result = $this->exp->expand('abc\?');
        $this->assertCount(1, $result);
        $this->assertContains('abc?', $result);
    }
}
