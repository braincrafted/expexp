RegExp Expansion
================

RegExp Expansion is a experimental tool to expand regular expressions.

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

Usage
-----

Do not use in production!

	$r = new RegExpExpansion('abc|xyz');
	$result = $r->expand();

More examples can be found in the test cases.



