ExpExp
======

ExpExp expands expressions. That's kinda the opposite of what regular expressions do.

For example `ab(cd|[xy])` expands to

- `abcd`,
- `abx` and
- `aby`.

[![Build Status](https://travis-ci.org/braincrafted/expexp.png?branch=master)](https://travis-ci.org/braincrafted/expexp)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/braincrafted/expexp/badges/quality-score.png?s=5a34a2a951572f0b1c5eebf5803c756c6d15fc32)](https://scrutinizer-ci.com/g/braincrafted/expexp/)
[![Code Coverage](https://scrutinizer-ci.com/g/braincrafted/expexp/badges/coverage.png?s=7b1c10126404a7f6e8c816edb239e89c82ef9c8a)](https://scrutinizer-ci.com/g/braincrafted/expexp/)

Author
------

- [Florian Eckerstorfer](http://florian.ec) ([Twitter](http://twitter.com/Florian_), [App.net](http://app.net/florian))

Features
--------

The following expressions can be expanded by the library:

**Disjunction:**

	abc[xyz]

will be expanded to

- `abcx`
- `abcy`
- `abcz`

**Named character classes:**

Instead of listing all disjunct characters, you can also select from a set of available character classes:

- `upper` contains uppercase characters (from ASCII)
- `lower` contains lowercase characters (from ASCII)
- `digit` contains digits
- `space` contains space characters
- `punct` contains punctuation characters

You can use named character classes by wrapping them in colons:

    [:upper:]

**Dot Operator:**

	abc.

will be expanded to

- `abcA`
- `abcB`
- …

The Dot opterator does not expand to every character, but only to `A-Za-z0-9_`.

**Parantheses:**

	ab(c)

will be expanded to

- `abc`

**Repetition:**

The repetition operator allows to repeat the previous character(s). If only one value is given the previous character is repeated that often, if two values are given the character is multiplied with each value in the given range. `{,3}` is the same as `{0,3}`.

    a{3}

will expand to

- `aaa`

Or with a minimum and a maximum value:

    a{1,3}

will expand to

- `a`
- `aa`
- `aaa`

This also works with disjunctions and parentheses.

**Alternation:**

	abc|xyz

will be expanded to

- `abc`
- `xyz`

**Optional:**

	abc?

will be expanded to

 - `abc`
 - `ab`

This also works with parantheses:

	abc(xyz)?

will be expanded to

- `abc`
- `abcxyz`

The optional operator has thus the same effect as `{0,1}`.


More examples
-------------

<table>
    <thead>
        <tr>
            <th>Pattern</th>
            <th>Count</th>
            <th>Expansion</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><code>abc</code></td>
            <td>1</td>
            <td><code>abc</code></td>
        </tr>
        <tr>
            <td><code>ab(c)</code></td>
            <td>1</td>
            <td><code>abc</code></td>
        </tr>
        <tr>
            <td><code>[abc]</code></td>
            <td>3</td>
            <td><code>a</code>, <code>b</code>, <code>c</code></td>
        </tr>
        <tr>
            <td><code>a{3}</code></td>
            <td>1</td>
            <td><code>aaa</code></td>
        </tr>
        <tr>
            <td><code>a{}</code></td>
            <td>1</td>
            <td><code>a</code></td>
        </tr>
        <tr>
            <td><code>a{1,3}</code></td>
            <td>3</td>
            <td><code>a</code>, <code>aa</code>, <code>aaa</code></td>
        </tr>
        <tr>
            <td><code>a{,3}</code></td>
            <td>4</td>
            <td><code></code>, <code>a</code>, <code>aa</code>, <code>aaa</code></td>
        </tr>
        <tr>
            <td><code>a(bc){2}</code></td>
            <td>1</td>
            <td><code>abcbc</code></td>
        </tr>
        <tr>
            <td><code>a(bc){1,2}</code></td>
            <td>2</td>
            <td><code>abcbc</code>, <code>abc</code></td>
        </tr>
        <tr>
            <td><code>a(bc){,2}</code></td>
            <td>3</td>
            <td><code>a</code>, <code>abc</code>, <code>abcbc</code></td>
        </tr>
        <tr>
            <td><code>[ab]{2}</code></td>
            <td>2</td>
            <td><code>aa</code>, <code>bb</code></td>
        </tr>
        <tr>
            <td><code>ab.</code></td>
            <td>63</td>
            <td><code>abA</code>, <code>abB</code>, <code>aba</code>, <code>ab0</code>, <code>ab_</code>, ...</td>
        </tr>
        <tr>
            <td><code>abc|xyz</code></td>
            <td>2</td>
            <td><code>abc</code>, <code>xyz</code></td>
        </tr>
        <tr>
            <td><code>a|b|c</code></td>
            <td>3</td>
            <td><code>a</code>, <code>b</code>, <code>c</code></td>
        </tr>
        <tr>
            <td><code>ab(c|d)</code></td>
            <td>2</td>
            <td><code>abc</code>, <code>abd</code></td>
        </tr>
        <tr>
            <td><code>ab(cde|[xyz])</code></td>
            <td>4</td>
            <td><code>abcde</code>, <code>abx</code>, <code>aby</code>, <code>abz</code></td>
        </tr>
        <tr>
            <td><code>abc?</code></td>
            <td>2</td>
            <td><code>abc</code>, <code>ab</code></td>
        </tr>
        <tr>
            <td><code>abc(xyz)?</code></td>
            <td>2</td>
            <td><code>abc</code>, <code>abcxyz</code></td>
        </tr>
    </tbody>
</table>


Usage
-----

Instantiate the object and call the `expand()` method with the pattern:

    use Bc\ExpExp\ExpExp;

	$e = new ExpExp();
	$result = $e->expand('abc|xyz');

More examples can be found in the test cases.


Changelog
---------

### Version 0.2.2 (2013-10-20)

- Dot operator matches `word` character class

### Version 0.2.1 (2013-10-19)

- Named character classes

### Version 0.2 (2013-10-19)

- Changed namespace to `Braincrafted`
- Added repetition operator `{}`
- Completely rewritten to be easier and better extensible
- Improved test suite

### Version 0.1.1 (2013-10-16)

- Better code style
- Better in-code documentation

### Version 0.1 (2013-10-16)

- Moved to `Bc` namespace
- Call `expand()` with pattern
- Better documentation


License
-------

ExpExp is licensed under The MIT License. See the `LICENSE` file in the projects root directory for more information.

