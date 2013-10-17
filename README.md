ExpExp
======

ExpExp expands expressions. That's kinda the opposite of what regular expressions do.

For example `ab(cd|[xy])` expands to

- `abcd`,
- `abx` and
- `aby`.

[![Build Status](https://travis-ci.org/braincrafted/expexp.png?branch=master)](https://travis-ci.org/braincrafted/expexp)

Author
------

- [Florian Eckerstorfer](http://florian.ec)

Features
--------

The following expressions can be expanded by the library:

**Disjunction:**

	abc[xyz]

will be expanded to

- `abcx`
- `abcy`
- `abcz`

**Dot Operator:**

	abc.

will be expanded to

- `abcA`
- `abcB`
- …

The Dot opterator does not expand to every character, but only to `A-Za-z0-9-`.

**Parantheses:**

	ab(c)

will be expanded to

- `abc`

**Multiplication:**

The multiplication operator allows to repeat the previous character. If only one value is given the previous character is repeated that often, if two values are given the character is multiplied with each value in the given range. `{,3}` is the same as `{0,3}`.

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

Usage
-----

Instantiate the object and call the `expand()` method with the pattern:

    use Bc\ExpExp\ExpExp;

	$e = new ExpExp();
	$result = $e->expand('abc|xyz');

More examples can be found in the test cases.


Changelog
---------

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

