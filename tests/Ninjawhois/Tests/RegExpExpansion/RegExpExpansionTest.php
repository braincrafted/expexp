<?php

namespace Ninjawhois\Tests\RegExpExpansion;

use Ninjawhois\RegExpExpansion\RegExpExpansion;

require_once __DIR__ . '/../../../../src/Ninjawhois/RegExpExpansion/RegExpExpansion.php';

class RegExpExpansionTest extends \PHPUnit_Framework_TestCase
{

    public function testExpandSimpleDisjunction()
    {
    	$r = new RegExpExpansion('[abc]xyz[abc]');
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
    	$r = new RegExpExpansion('\[abc\]xyz\[abc\]');
    	$result = $r->expand();
    	$this->assertCount(1, $result);
    	$this->assertContains('[abc]xyz[abc]', $result);

		$r = new RegExpExpansion('\\\abc');
    	$result = $r->expand();
    	$this->assertCount(1, $result);
    	$this->assertContains('\abc', $result);
    }

}
