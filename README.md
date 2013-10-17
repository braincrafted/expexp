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

