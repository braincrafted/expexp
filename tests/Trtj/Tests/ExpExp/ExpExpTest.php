<?php

namespace Trtj\Tests\ExpExp;

use Trtj\ExpExp\ExpExp;

require_once __DIR__ . '/../../../../src/Trtj/ExpExp/ExpExp.php';

class ExpExpTest extends \PHPUnit_Framework_TestCase
{

    public function testExpandSimpleDisjunction()
    {
    	$r = new ExpExp('[abc]xyz[abc]');
    	$result = $r->expand();
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

    public function testExpandEscapedDisjunction()
    {
    	$r = new ExpExp('\[abc\]xyz\[abc\]');
    	$result = $r->expand();
    	$this->assertCount(1, $result);
    	$this->assertContains('[abc]xyz[abc]', $result);

		$r = new ExpExp('\\\abc');
    	$result = $r->expand();
    	$this->assertCount(1, $result);
    	$this->assertContains('\abc', $result);
    }

    public function testExpandDotOperator()
    {
    	$r = new ExpExp('ab.');
    	$result = $r->expand();
    	$this->assertCount(63, $result);
    	$this->assertContains('abA', $result);
    	$this->assertContains('aba', $result);
    	$this->assertContains('ab0', $result);
    	$this->assertContains('ab-', $result);

    	$r = new ExpExp('ab\.');
    	$result = $r->expand();
    	$this->assertCount(1, $result);
    	$this->assertContains('ab.', $result);
    }

	public function FunctionName($value='')
	{
		$r = new ExpExp('ab(c)');
		$result = $r->expand();
		$this->assertCount(1, $result);
		$this->assertContains('abc', $result);
	}

    public function testExpandAlternation()
    {
		$r = new ExpExp('abc|xyz');
		$result = $r->expand();
		$this->assertCount(2, $result);
		$this->assertContains('abc', $result);
		$this->assertContains('xyz', $result);

		$r = new ExpExp('ab(c|d)');
		$result = $r->expand();
		$this->assertCount(2, $result);
		$this->assertContains('abc', $result);
		$this->assertContains('abd', $result);

		$r = new ExpExp('ab(cde|[xyz])');
		$result = $r->expand();
		$this->assertCount(4, $result);
		$this->assertContains('abcde', $result);
		$this->assertContains('abx', $result);
		$this->assertContains('aby', $result);
		$this->assertContains('abz', $result);
    }

    public function testOptional()
    {
    	$r = new ExpExp('abc?');
    	$result = $r->expand();
    	$this->assertCount(2, $result);
    	$this->assertContains('abc', $result);
    	$this->assertContains('ab', $result);

    	$r = new ExpExp('abc(xyz)?');
    	$result = $r->expand();
    	$this->assertCount(2, $result);
    	$this->assertContains('abc', $result);
    	$this->assertContains('abcxyz', $result);
    }

}
