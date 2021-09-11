# 🦸🏼‍♂️ - Eloquent Super
Lightweight MTI (Multiple Table Inheritance) support for Eloquent models.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dive-be/eloquent-super.svg?style=flat-square)](https://packagist.org/packages/dive-be/eloquent-super)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/dive-be/eloquent-super.svg?style=flat-square)](https://packagist.org/packages/dive-be/eloquent-super)

## What is "Multiple Table Inheritance" exactly?

MTI allows one to have separate database tables for each "sub class" that shares a common "super class".

### "Can't I just define a type column in my table and call it a day?"

Well, it depends. If the **only** thing you'd like to do is adding specific behavior to each sub type (`user` - `admin` for example), then [Single Table Inheritance](https://github.com/calebporzio/parental) is definitely the better choice here. 

However, if the sub types have very different data fields, then MTI is the better tool. Using STI in this case will cause the table in question to have **a lot** of `NULL` columns.

## What problem does this package solve?

### Short answer

As a matter of fact, it solves absolutely **nothing**. "Why this package, then?" you may ask. Well, read on.

### Long answer

You see, Eloquent already gives us the ability to define polymorphic relationships. The only thing you need to start leveraging MTI capabilities in Eloquent is a `MorphOne` relationship. This package adds a nice DX layer on top of the existing functionality, so it is a tad nicer to work with these kind of tightly coupled relationships. 

So, the "meat and potatoes" of this package is **delegating calls** to the defined `super` relationship (and a couple more things). There is no real "parent" class in an object oriented sense. It is a conscious decision to not sprinkle too much magic on the models.

## Installation

```shell
composer require dive-be/eloquent-super
```

## Usage

### Super / parent class 👱🏻‍♂️

#### Migrations

The super model must define a morphs relationship that follows Laravel's naming conventions: the model's singular name in snake case + `able`.

```php
Schema::create('addresses', static function (Blueprint $table) {
    $table->id();
    $table->foreignId('country_id')->constrained();
    $table->morphs('addressable'); // ==> mandatory
    
    // ... other columns
});
```

#### Class definition

The super class **must** define a fillable array in order to determine which attributes belong to which database tables. Without it, there is no way to distinguish the super's columns from the sub's columns.

```php
class Address extends Model
{
    protected $fillable = ['city', 'country_id', 'street', 'postal_code'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
```

### Sub / child classes 👶🏼

It is not mandatory for the sub classes to define a fillable array. Setting `$guarded` to an empty array is perfectly fine as well.

#### Class definition

```php
class ShippingAddress extends Model
{
    use \Dive\EloquentSuper\InheritsFromSuper;

    protected $fillable = ['email', 'phone', 'contact_person', 'is_expedited', 'courier'];
    
    protected function getSuperClass(): string
    {
        return Address::class;
    }
}
```

```php
class InvoiceAddress extends Model
{
    use \Dive\EloquentSuper\InheritsFromSuper;

    protected $fillable = ['company_id', 'email', 'fax', 'phone', 'language'];
    
    protected function getSuperClass(): string
    {
        return Address::class;
    }
}
```

## Capabilities 💪

### Partitioning of data when saving a sub model

```php
$address = ShippingAddress::create($request->validated());

$address->getAttributes(); // 'email', 'phone', 'contact_person', 'is_expedited', 'courier'
$address->super->getAttributes(); // 'city', 'country_id', 'street', 'postal_code'
```

### Attribute / relationship retrieval from super model

```php
$address->city; // Ghent
$address->super->city; // Ghent

$address->country; // App\Models\Country { #2981 }
$address->super->country; // App\Models\Country { #2981 }
```

### Deleting the super along with the sub model

```php
$address->delete(); // Database transaction in the background
```

> Note: only the sub model will be trashed if both the super and sub use the "SoftDeletes" trait

## A note on always eager loading the "super" relationship 📣

It does not make sense for the sub model to exist without its complementary data from the super model. By having two tables, we are able to achieve a normalized database, but in code, it only makes sense when they coexist as a whole. 

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email oss@dive.be instead of using the issue tracker.

## Credits

- [Muhammed Sari](https://github.com/mabdullahsari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
