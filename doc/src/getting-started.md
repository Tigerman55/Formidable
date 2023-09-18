# Creating your first form

In Formidable, forms handle all your POST array input[^file-uploads], validate it, and return typed form data. To
achieve this, each form gets a mapping assigned which specifies how to handle the required data types. Let's look at a
simple example:

```php
<?php

declare(strict_types=1);

use Formidable\Form;
use Formidable\Mapping\FieldMappingFactory;
use Formidable\Mapping\ObjectMapping;

$form = new Form(new ObjectMapping([
    'name'         => FieldMappingFactory::text(1),
    'emailAddress' => FieldMappingFactory::emailAddress(),
], PersonFormData::class));
```

This will create a basic form with two fields:

- a name (which must be at least one character long)
- an email address

The `ObjectMapping` class defines how the form fields map to a typed form data object. The form data object is an intermediate transfer object, distinct from an entity. It is the bridge between the form and the entity. It is responsible for defining and enforcing data types and input validation rules. Once data has been mapped into the form data object with no errors, it can be considered filtered, valid, and type safe, making it trivial to populate an entity or database row.

Formidable's `ObjectMapping` requires that your Form DTO implements our interface `FormDataTransferObjectInterface` which specifies a static factory method. When data is extracted from the form data for unbinding (validation step and converting to good PHP types), all values will be extracted via reflection from the object, so the related dto property names must match the form mapping names you have specified.

The PersonFormData from the above example could look like this:

```php
<?php

declare(strict_types=1);

use Formidable\FormDataTransferObjectInterface;

final class PersonFormData implements FormDataTransferObjectInterface
{
    public function __construct(public readonly string $name, public readonly string $emailAddress)
    {
    }

    public static function fromArrayOfArguments(array $arguments) : FormDataTransferObjectInterface
    {
        return new self(...$arguments);
    }
}
```
# Using the form to handle input

Now that the form is created, let's use it to validate some input. Formidable is build with PSR-7 compatibility in mind,
so when you are using a framework based on PSR-7, it becomes really easy to inject your data:

```php
<?php
$form = $form->bindFromRequest($psr7ServerRequest);

if (!$form->hasErrors()) {
    /* @var $personFormData PersonFormData */
    $personFormData = $form->getValue();

    // You may use $personFormData now to populate some entity or store the data in a database.
}

// At this point, the form validation found an error, so you should re-display the form.
```

!!!note "A note about immutability"

    You may have noticed that the `$form` variable was re-assigned when binding the request. This is because everything
    in Formidable is immutable. Thus, when you bind a request to a form or try to make any other changes, it will
    actually clone itself and return the clone with the changes applied. This guarantees that the original form instance
    is stateless and can be re-used in other places without ambiguous state.

# Rendering the form

Rendering forms can be done manually, or by using helpers such as those provided by Formidable. The process of doing so
is described in detail in the [Rendering Forms](rendering-forms.md) section.

[^file-uploads]: Formidable doesn't handle file uploads at this time, as we currently consider that out of scope.
