ExpExp
======

ExpExp is a small library that allows to expand an expression similar to regular expressions.

By [Florian Eckerstorfer](http://florianeckerstorfer.com)

Support
-------

The following expressions can be expanded by the library:

**Disjunction:**

	abc[xyz]
	
will be expanded to

* `abcx`
* `abcy`
* `abcz`

**Dot Operator:**

	abc.

will be expanded to

* `abcA`
* `abcB`
* …

The Dot opterator does not expand to every character, but only to `A-Za-z0-9-`.

**Parantheses:**

	ab(c)
	
will be expanded to

* `abc`
	
**Alternation:**

	abc|xyz
	
will be expanded to

* `abc`
* `xyz`

**Optional:**

	abc?
	
will be expanded to

 * `abc`
 * `ab`

This also works with parantheses:

	abc(xyz)?

will be expanded to

* `abc`
* `abcxyz`

Usage
-----

Do not use in production!

	$r = new ExpExp('abc|xyz');
	$result = $r->expand();

More examples can be found in the test cases.



