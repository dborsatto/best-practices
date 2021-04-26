This document contains a list of best practices and a style guide for development with PHP, Symfony and Doctrine.

## Style guide

### General

#### Files

Files for all languages (PHP, Javascript, Twig, YAML, XML, etc) must end with an empty line (the line feed character, LF).

#### Line length

Try to remain below 120 characters. Longer lines should be broken up, with the only exceptions being strings, which can go above the limit without requiring breaking up.

In the event of function parameters being split into multiple lines, each parameter must be placed in a single line:

```php
// Yes
$service->action(
    $parameter1,
    $parameter2,
    $parameter3,
    $parameter4
);

// No
$service->action($parameter1,
    $parameter2, $parameter3, $parameter4);
```

There should not be "preemptive" line breaks. If some content can fit in a line, there should be no real reason to introduce a line break which would cause an extra level of indentation.

#### Type declarations and strict typing

All new code should declare in and out parameters as type declarations. All new files should have `declare(strict_types=1);` added at the beginning.

### Naming

#### Classes

In a Symfony project, it makes sense to use the same conventions as the Symfony code and use the prefix `Abstract` and the `Interface` and `Exception` suffix.

```text
DateProviderInterface
InvalidDateException
AbstractDateProvider
```

While sometimes it may seem redundant, it is always preferable to employ a clear convention rather than discussing on a case-by-case scenario.

#### Variables

Variable names must not be too long or too short. Although there is no hard limit either way, it is common sense to not go below 3 characters long.

#### Casing

Variables and methods should be named using camel case, whereas classes and class-like structures (traits, interfaces, etc) should use pascal case.

### PHPDoc

From PHP 7.0 onwards, it is possible to define in and out parameter types without the need to use PHPDoc. For this reason, the use of PHPDoc when no further information is added is considered redundant and to be avoided.

When further information _can_ be added using PHPDoc, _all_ information should then be added because a small duplication is preferable to having two partial sources of data which then need to be combined by the reader. These are the main situations when PHPDoc use is required.

#### Documenting exceptions

If within a function an exception is thrown (both directly with `throw`, or inside another function which is called), it is _mandatory_ to add a `@throws` annotation in the PHPDoc block. The only exception to this rule is when having to work with objects like `DateTimeImmutable` which technically can throw an exception during construction, but only when the given string is invalid and often we can be reasonable sure that this will not be the case. In all other scenarios, exceptions must be documented.

#### Documenting arrays

In order for static analysis tools to work best with a codebase, all arrays should be documented. PHP arrays can actually represent multiple things, so it's important to always try to convey their real meaning.

This is an example of how common structures should be documented. Further documentation can be found on the [Psalm website](https://psalm.dev/docs/annotating_code/type_syntax/array_types/).

```php
/** @var list<int> */
$array = [1, 2];
/** @var list<string> */
$array = ['foo', 'bar'];
/** @var list<stdClass> */
$array = [new stdClass(), new stdClass()];

/** @var array<string, int> $array */
$array = [
    'foo' => 1,
    'bar' => 2,
];
/** @var array<string, stdClass> $array */
$array = [
    'foo' => new stdClass(),
    'bar' => new stdClass(),
];
/** @var array<int, int> $array */
$array = [
    5 => 50,
    10 => 100,
];
/** @var array<int, array<int>> $array */
$array = [
    5 => [50],
    10 => [100, 200],
];
/** @var array<int, array<string, string>> $array */
$array = [
    5 => ['foo' => 'foo'],
    10 => ['bar' => 'bar', 'baz' => 'baz'],
];
```

Important: even though there is a way of documenting heterogeneous arrays, it is recommended to avoid using them and instead prefer the creation of ad-hoc value objects.

### Misc

#### Importing classes and other elements

For the sake of consistency (and a slight performance benefit), all elements that can be `use`d should be `use`d. This means that all classes (including those in the global namespace), all functions and all constants should be added to the `use` list at the beginning of a file. This can be easily done automatically using PHP-CS-Fixer, and avoids cluttering the code with backslashes for no real reason. All uses in the rest of the code must then refer to the imported symbol, without any namespace reference added to it.

#### Fluent interfaces

The rule of thumb with fluent interfaces is to have one _action_ per line. It's considered bad practice to use fluent interfaces within control structures and function calls.

```php
// Yes
$this->service->getStartDate()
    ->format('Y-m-d');

// No
$this
    ->service
    ->getStartDate()
    ->format('Y-m-d');

// No
$this->service->getStartDate()->format('Y-m-d');
```

Accessing a property with `$this->property` does not count as an _action_, so as it's shown in the first example, having `$this->service->getStartDate()` in one line is valid.

Exception: when function parameters must be broken up in separate lines, it may be helpful to not have an action in the first line in order to better align the following calls:

```php
$builder
    ->add('startDate', DateType::class, [
        'required' => true,
    ])
    ->add('endDate', DateType::class, [
        'required' => true,
    ]);
```

When breaking up a fluent interface in multiple lines, the basic unit of indentation (4 spaces) must be used. Indentation that changes according to the length of the name of a variable is considered brittle and should be avoided.

```php
// Yes: renaming any variable will not affect other lines
$queryBuilder = $entityManager->createQueryBuilder()
    ->from(MyEntity::class, 'e');

// No: renaming the entity manager or query builder variable
// will cause all following lines to change indentation
$queryBuilder = $entityManager->createQueryBuilder()
                              ->from(MyEntity::class, 'e');
```

#### Constant visibility

Just like class properties, constants must have their visibility modifier declared. Also just like properties, private is always preferrable to public.

#### Multiline ternary conditions

Whenever a ternary operator requires to be broken up into multiple lines, the recommended way of doing this is the following:

```php
$value = $condition
    ? 'value if true'
    : 'value if false';
```

This way every result is clearly visible depending on which symbol the line starts with, with `?` at the beginning of the `true` path, and `:` at the beginning of the `false` path.

## Best practices

### Generic

#### Accessing a property from within a class

A property should be accessed directly, and using a getter for this should be considered an anti-pattern:

```php
// No
$this->getAuthor()->getName();

// Yes
$this->author->getName();
```

#### Variable assignment within control structures

Except for a few specific cases, assignign values to a variable within a control structure (such as `if`, `while`, etc) should be considered a bad practice.

```php
// Yes:
$author = $blogPost->getAuthor();
if ($author) {

// No:
if ($author = $blogPost->getAuthor()) {
```

#### Domain and application exceptions

Whenever a public method must throw an exception, this must be either a domain or application-level exception and never a PHP built-in one. Domain exceptions should extend an application-specific `DomainException` and not from others like `RuntimeException` or `InvalidArgumentException`, because a domain exception's goal should be to communicate a message specific to the domain, and categorizing it using a specific PHP built-in exception does not have any further advantage.

Not all exceptions are domain exceptions: according to the separations in 3 levels (domain, application, infrastructure) of a "ports and adapters" architecture (also known as "hexagonal" architecture), some exceptions can be raised from contexts that are not domain-related. For this reason there should be a base `ApplicationException` that serves the same purpose of `DomainException`, but it's used within application boundaries.

The infrastructure layer does not have a base exception because infrastructure components are integrated using domain or application ports (interfaces), and these interfaces must define themselves which exception can be thrown, therefore making these exceptions part of the domain layer.

[This article](https://medium.com/@davide.borsatto/not-just-for-exceptional-circumstances-7692f2775a5a) discusses further the use of exceptions.

#### Named constructors for exceptions

While not mandatory, it is strongly recommended to use named constructors for creating exceptions:

```php
class BlogPostException extends DomainException
{
    public static function titleTooShort(string $title): self
    {
        return new self(sprintf('The title "%s" is too short for a blog post', $title));
    }
}
```

This type of use makes exception messages more consistent, and has the side effect of making error messages easily accessible in unit test contexts, so it's possible to test that a function has thrown *exactly* the expected exception.

#### Use of DateTimeImmutable

Wherever possibile, it is preferable to use `DateTimeImmutable` in place of `DateTime`. This is especially true for in and our parameters of functions, as the immutability guarantees that the value will not be changed by the function itself.

#### Difference between Value Object and Data Transfer Object

Within the context of a Symfony application it is useful to agree on these definitions:

- A _value object_ is an immutable object that once it is built, is in a valid state. These objects require all mandatory parameters to be passed using the constructor, and provide no nullable parameters unless the nullability is explicitely defined by domain rules.
  If more ways to create a value object are found, these ways can be expressed using different named constructors. It's considered good practice not to mix usage of regular constructor and named constructors: should the second ones be used, it is recommended to define the actual constructor as private.
  Value objects can define getters to access properties, but no setters should be available. They can have methods that provide some sort of computed data using the internal state.
  As they are simple containers of data, they shoud not have access to objects that can be classified as services. The only parameters that can be passed to the constructors are those need to determine the internal state of the value object, but they can't be used to provide data computation functionality.
  According to DDD guidelines, a value object represents a standalone domain concept. Two value objects are considered the _same_ (regardless of PHP identity) if all of their properties have the same value. This is unlike entities, which are supposed to have some sort of identifier that determines the identity.
- A _data transfer object_ is an object that can be created in an invalid state, and provides ways (methods or public properties) to change its internal state. It can be used as an object where data can be "accumulated" from different sources, and maybe as an object that is used as an intermediate state between forms and entities, as Symfony forms require that all object properties be nullable.

Because of their immutability, value objects are usually preferable to data transfer objects. The suffix _VO_ is to be avoided, as the object should represent a domain concept, whereas the suffix _DTO_ can be used as they represent technical implementations.

#### Always prefer structured, immutable data to unstructured arrays

The use of arrays to transfer data from two different objects if strongly discouraged, except for when they are simple ordered lists of elements (so their type can be defined as `list<Type>`). The reason for this is that can represent heterogeneous and hard to document values, even when using proper type annotations. For example, a type `array<string, MyClass>` says that keys are strings, but it does not convey what these strings are and therefore it needs further documentation. Using these structures within a class is not a problem as the context is completely available in the same place, but they are to be avoided for communication between different classes as encapsulation is broken due to the receiver having to know implementation details in order to use the value. For these reasons, uses like these are considered bad practices:

```php
class MyService
{
    public function getCredentials(): array
    {
        return [$username, $password];
    }
    
    public function getData(): array
    {
        // ...
        return [
            'name' => $name,
            'age' => $age,
            // ...
        ];
    }
}
```

Situations like this warrant the creation of specific objects, preferably immutable, which have the side effect of using less memory as PHP can better optimize their usage.

#### Avoid using abstract classes as type declarations

An abstract class does not represent a promise, but rather a partial implementation. For this reason, it is semantically incorrect to use it as type declaration, as a type declaration defines the promise of a behavior (which should be defined by an interface) or a very specific implementation (which uses an actual class). Using an abstract class is a wrong way of categorizing objects that share some behavior, and for this reason interfaces should be used instead.

#### FQCN usage

The form `MyClass::class` should be used whenever possible instead of having to manually type the FQCN of a class. This use enables proper code analysis and removed the inconvenience of having to refer to classes using strings, which are inherently brittle and would be difficult to refactor. This syntas should also be used in entity annotations to refer to other classes (like related entities).

```php
// Yes
use App\Code\MyClass;

public function getClassName(): string
{
    return MyClass::class;
}

// No
public function getClassName(): string
{
    return 'App\Code\MyClass';
}
```

#### Object to string conversion

While PHP natively offers the magic method `__toString()`, which is called automagically whenever an object is converted to a string, this approach relies on implicit behavior which [can be tricky to debug](https://github.com/ShittySoft/symfony-live-berlin-2018-doctrine-tutorial/pull/3) and hard to analyze (there is no easy way to find where some code is casting an object to string).

For this reason, relying on the `__toString()` method is considered a bad practice, and an explicit `toString()` method should be used instead. For scenarios where the `__toString()` method is required by some specific use, both methods must be implemented and `__toString()` must forward to the real implementation.

```php
public function __toString(): string
{
    return $this->toString();
}

public function toString(): string
{
    return '...';
}
```

### Symfony

# Validation using POST_SUBMIT events in forms

Form validations happen during the `POST_SUBMIT` even, using priority `0`. If an event listener is defined for this even, it must be remembered that according to the priority, the form may or may not have been validated.

# Forcing rendering of collection form fields in Twig

If in a Twig template a collection form field is rendered using a `for` loop, it must be taken into account that if the collection is empty, Twig will not handle the state properly and will not consider that field to have been rendered. For this reason, it will be wrongly rendered whenever `form_rest` is called.

To avoid this, a manual call to `setRendered` should be placed as safeguard:

```twig
{% for field in form.fields %}
  //
{% else %}
  {% do form.fields.setRendered() %}
{% endfor %}
```

#### Service injection and service locators

Service retrieval using `$container->get($serviceId)` should be considered deprecated and must be avoided, with proper dependency injection using the constructor to be used instead. For the same reason, accessing repositories using `EntityManagerInterface::getRepository($entityName)` is also considered deprecated.

#### Event dispatchers and subscribers

Every event subscriber should subscribe to a single event. The method that handles the event should be called `handle`.

#### In EntityType forms, prefer choices option over queryBuilder

Creating a form that extends `EntityType`, choices can be set using two options: `choices`, which accepts a parameter of type `list<MyEntity>`, or `queryBuilder`, which accepts either a query builder or a callback that must return the query builder.

Things being equal, `choices` should be the preferred option, as the form type can correctly declare its dependency towards the repository, and the repository itself can be configured to return entities and not query builder objects, a behavior which violate the repository pattern.

#### In EntityType forms, configure choice_label as callback

Using the option `choice_label` it is possible to define how Symfony will convert an object into a string (using `__toString()` as fallback). When a custom method must be used, there are two approaches:

```php
'choice_label' => 'customMethod',
//
'choice_label' => function (MyEntity $entity): string {
    return $entity->customMethod();
},
```

The second option, despite longer, is preferable as the method will no longer be called "magically" by Symfony, with the code will be easier to analyze and the uses of the `customMethod` easier to track within the project.

#### Collections

Handling with collections must be seen as an implementation details within the entities, and ideally they should not be exposed by the public API. This has several advantages:
- There is no doubt in what type will be returned (which will always be `list<MyEntity>`)
- Type hinting can be used without having to resort to the double annotation `Collection|list<MyEntity>`
- A collection can not be updated from outside the entity
- Manually handling `setX` methods, we avoid possibile bugs due to lazy loading or overwriting of whole collections

This is a complete example of a correct way of defining methods that work on a collection:

```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
class Author
{
    /**
     * @var Collection|list<BlogPost>
     *
     * @ORM\OneToMany(
     *     targetEntity=BlogPost::class,
     *     mappedBy="author",
     *     cascade={"persist"},
     *     orphanRemoval=true
*)
     */
    private $blogPosts;

    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
    }
    
    /**
     * @return list<BlogPost>
     */
    public function getBlogPosts(): array
    {
        return $this->blogPosts->toArray();
    }
    
    public function addBlogPost(BlogPost $blogPost): void
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts->add($blogPost);
        }
    }

    public function removeBlogPost(BlogPost $blogPost): void
    {
        $this->blogPosts->removeElement($blogPost);
    }
    
    /**
     * @param list<BlogPost> $blogPosts
     */
    public function setBlogPosts(array $blogPosts): void
    {
        $this->blogPosts->clear();
        foreach ($blogPosts as $blogPost) {
            $this->blogPosts->add($blogPost);
        }
    }
}
```
In the property definition, the interface `Collection` should be used instead of the `ArrayCollection` implementation, which is used to initialize the value. The reason for this is that during execution, Doctrine will overwrite the property using a different `Collection` implementation, which enables lazy loading and other features.

#### Repository definitions

If Doctrine repositories need to extend a base service (which should not be the case), they must be defined by extending `Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository`, so there is no need to manually configure the repository and it can be used with autowiring.

#### Repository return values

The repository pattern defines that only entities or simple computation results (like an int for a count operation, or a boolean) should be returned. Returning a query builder object is an infrastructural leak, whereas converting entities into value objects implies that the repository is aware of implementation details of the specific use.

#### Magic "find" methods in repositories

Doctrine provides magic methods in its base repository services, which can be used as shortcut. The use of these methods, however, should be seen as bad practice, and the definition of custom and explicit methods should be preferable.
