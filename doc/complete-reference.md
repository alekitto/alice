# Complete Reference

1. [Creating Fixtures](#creating-fixtures)
  1. [YAML](#yaml)
  1. [PHP](#php)
1. [Fixture Ranges](#fixture-ranges)
1. [Calling Methods](#calling-methods)
1. [Specifying Constructor Arguments](#specifying-constructor-arguments)
1. [Using a factory](#using-a-factory)
1. [Optional Data](#optional-data)
1. [Handling Unique Constraints](#handling-unique-constraints)


## Creating Fixtures

### YAML

The most basic functionality of this library is to turn flat yaml files into
objects. You can define many objects of different classes in one file as such:

```yaml
Nelmio\Entity\User:
    user0:
        username: bob
        fullname: Bob
        birthDate: 1980-10-10
        email: bob@example.org
        favoriteNumber: 42

    user1:
        username: alice
        fullname: Alice
        birthDate: 1978-07-12
        email: alice@example.org
        favoriteNumber: 27

Nelmio\Entity\Group:
    group1:
        name: Admins
```

This works fine, but it is not very powerful and is completely static. You
still have to do most of the work. Let's see how to make this more interesting.

### PHP

You can also specify fixtures in PHP by returing an array where each key with
the following structure:

```php
<?php

return [
    'Nelmio\Alice\support\models\User' => [
        'user1' => [
            'username' => 'bob',
            'fullname' => 'Bob',
        ],
        'user2' => [
            'username' => 'alice',
            'fullname' => 'Alice',
        ],
    ],
];
```


## Fixture Ranges

The first step is to let Alice create many copies of an object for you
to remove duplication from the yaml file.

You can do that by defining a range in the fixture name:

```yaml
Nelmio\Entity\User:
    user{1..10}:
        username: bob
        fullname: Bob
        birthDate: 1980-10-10
        email: bob@example.org
        favoriteNumber: 42
```

Now it will generate ten users, with IDs `user1` to `user10`. Pretty good but
we only have 10 bobs with the same name, username and email, which is not
so fancy yet.

You can also specify a list of values instead of a range:

```yaml
Nelmio\Entity\User:
    user_{alice, bob}:
        username: '<current()>'
        fullname: '<current()>'
        birthDate: 1980-10-10
        email: '<current()>@example.org'
        favoriteNumber: 42
```

>The `<current()>` function is a bit special as it can only be called in the
>context of a collection (list of values or a range).

>In the case of a list of values like the example above, it will return for the
>first fixture `user_alice` the value `alice`, and `bob` for the fixture
>`user_bob`.

>In the case of a range (e.g. `user{1..10}`), `<current()>` will return `1` for
>`user1`, `2` for `user2` etc.

>Using this function outside of this case will cause an exception.

To go further we the example above, we can just randomize data.


## Calling Methods

Sometimes though you need to call a method to initialize some more data, you
can do this just like with properties but instead using the method name and
giving it an array of arguments. For example let's assume the user class has
a `setLocation` method that requires a latitude and a longitude:

```yaml
Nelmio\Entity\User:
    user1:
        username: '<username()>'
        __calls:
            - setLocation: [40.689269, -74.044737]
```


## Specifying Constructor Arguments

When a constructor has mandatory arguments you must define it as explained
above, for example if the User required a username in the constructor you
could do the following:

```yaml
Nelmio\Entity\User:
    user1:
        __construct: ['<username()>']
```

If you specify `false` in place of constructor arguments, Alice will
instantiate the object without executing the constructor:

```yaml
Nelmio\Entity\User:
    user1:
        __construct: false
```


## Using a factory

**[TODO] Status: unimplemented; usable in with `__construct` in `__factory` but this will be deprecated once
`__factory` is out available and will be removed in 4.0**

If you want to call a static factory method instead of a constructor, you can
specify a hash as the constructor:

```yaml
Nelmio\Entity\User:
    user1:
        __factory: { create: ['<username()>'] }
```

If the static factory belongs to another class, you can call it as follows:

```yaml
Nelmio\Entity\User:
    user1:
        __factory: { Nelmio\User\UserFactory::create: ['<username()>'] }
```


## Optional Data

Some fields do not have to be filled-in, like the `favoriteNumber` in this
example might be personal data you don't want to share, to reflect this in
our fixtures and be sure the site works and looks alright even when users
don't enter a favorite number, we can make Alice fill it in *sometimes* using
the `50%? value : empty value` notation. It's a bit like the ternary operator,
and you can omit the empty value if `null` is ok as such: `50%? value`.

Let's update the user definition with this new information:

```yaml
Nelmio\Entity\User:
    user{1..10}:
        username: '<username()>'
        fullname: '<firstName()> <lastName()>'
        birthDate: '<date()>'
        email: '<email()>'
        favoriteNumber: '50%? <numberBetween(1, 200)>'
```

Now only half of the users will have a number filled-in.


## Handling Unique Constraints

Quite often some database fields have a unique constraint set on them, in which
case having the fixtures randomly failing to generate because of bad luck is
quite annoying. This is especially important if you generate large amounts of
objects, as otherwise you will most likely never encounter this issue.

By declaring the key as unique using the `(unique)` flag at the end, Alice
will make sure every element of this class that is created has a unique value
for that property. For example:

```yaml
Nelmio\Entity\User:
    user{1..10}:
        username (unique): '<username()>'
```

In a case of a method call or a constructor, you can specify the unique flag
like so:

```yaml
Nelmio\Entity\User:
    user{1..10}:
        __construct:
            0 (unique): '<username()>'
```

If the property or field in question is an array, the behaviour changes to apply
only to the fixture, i.e. the following will work:

```yaml
Nelmio\Entity\User:
    friends{1..2}:
        username (unique): '<username()>'
    user{1..2}:
        friends (unique): '@friends*' # array value
```

However the fields `user1#friends` and `user2#friends` will not have any duplicate.


« [Handling Relations](relations-handling.md) • [Getting Started](getting-started.md) »
