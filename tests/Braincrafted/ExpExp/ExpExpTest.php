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
     * @dataProvider expandProvider
     */
    public function testExpand($pattern, $expectedCount, array $elements)
    {
        $result = $this->exp->expand($pattern);

        $this->assertCount($expectedCount, $result);
        foreach ($elements as $element) {
            $this->assertContains($element, $result);
        }
    }

    public function expandProvider()
    {
        return [
            // Disjunction
            [ '[abc]', 3, [ 'a', 'b', 'c' ] ],
            [ '[abc]x[abc]', 9, [ 'axa', 'axb', 'axc', 'bxa', 'bxb', 'bxc', 'cxa', 'cxb', 'cxc' ] ],
            // Repetition
            [ 'a{3}', 1, [ 'aaa' ] ],
            [ 'a{}', 1, [ 'a' ] ],
            [ 'a{1,3}', 3, [ 'a', 'aa', 'aaa' ] ],
            [ 'a{,3}', 4, [ '', 'a', 'aa', 'aaa' ] ],
            // Parentheses
            [ 'ab(c)', 1, [ 'abc' ] ],
            // [ 'a(b(c))', 1, [ 'abc' ] ],
            // Parentheses + repetition
            [ 'a(bc){2}', 1, [ 'abcbc' ] ],
            [ 'a(bc){1,2}', 2, [ 'abc', 'abcbc' ] ],
            [ 'a(bc){,2}', 3, [ 'a', 'abc', 'abcbc' ] ],
            // Disjunction + repetition
            [ '[ab]{2}', 2, [ 'aa', 'bb' ] ],
            [ '[ab]{0,2}', 6, [ '', 'a', 'aa', 'b', 'bb' ] ],
            // Dot operator
            [ 'ab.', 63, [ 'abA', 'abB', 'aba', 'ab0', 'ab-' ] ],
            // Alternation
            [ 'abc|xyz', 2, [ 'abc', 'xyz' ] ],
            // Alternation in parentheses
            [ 'ab(c|d)', 2, [ 'abc', 'abd' ] ],
            // [ 'a(b|(c|d))', 3, [ 'ab', 'ac', 'ad' ] ],
            // Alternation + disjunction in parentheses
            [ 'ab(cde|[xyz])', 4, [ 'abcde', 'abx', 'aby', 'abz' ] ],
            // Optional
            [ 'abc?', 2, [ 'abc', 'ab' ] ],
            [ 'abc(xyz)?', 2, [ 'abc', 'abcxyz' ] ],
            // Escaped control characters
            [ '\[abc\]x', 1, [ '[abc]x' ] ],
            [ '\[abc]x', 1, [ '[abc]x' ] ],
            [ 'a\{3\}', 1, [ 'a{3}' ] ],
            [ 'a\{3}', 1, [ 'a{3}' ] ],
            [ 'abc\?', 1, [ 'abc?' ] ],
            [ 'ab\.', 1, [ 'ab.' ] ],
            [ 'a\|b', 1, [ 'a|b' ] ],
            [ '\(a\)', 1, [ '(a)' ] ],
            [ '\(a)', 1, [ '(a)' ] ],
            [ '\\\abc', 1, [ '\abc' ] ]
        ];
    }
}
