# Eloquent Super
Lightweight MTI (Multiple Table Inheritance) support for Eloquent models.

⚠️ Minor releases of this package may cause breaking changes as it has no stable release yet.

## What is "Multiple Table Inheritance"?

MTI allows one to have separate database tables for each "sub class" that shares a common "super class".

### "Can't I just define a type column in my table and call it a day?"

Well, it depends. If the **only** thing you'd like to do is adding specific behavior to each sub type (`user` - `admin` for example), then [Single Table Inheritance](https://github.com/calebporzio/parental) is definitely the better choice here. 

However, if the sub types have very different data fields, then MTI is the better tool. Using STI in this case will cause the table in question to have **a lot** of `NULL` columns.

## Installation

```shell
composer require dive-be/eloquent-super
```

## Usage

### Super / parent class

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

The super class **must** define a fillable array in order to determine which attributes belong to which database tables.

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

### Sub classes

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

## What problem does this package solve?

### Short answer

Actually, it solves absolutely **nothing**. "Why this package, then?" you may ask. Well, read on.

### Long answer

You see, Eloquent already gives us the ability to define polymorphic relationships. The only thing you need to start leveraging MTI capabilities in Eloquent is a `MorphOne` relationship. This package adds a nice DX layer on top of the existing functionality, so it is a tad nicer to work with these kind of tightly coupled relationships. 

So, the "meat and potatoes" of this package is **delegating calls** to the defined `super` relationship (and a couple more things). There is no real "parent" class in an object oriented sense. It is a conscious decision to not sprinkle too much magic on the models.

### Capabilities

Automatic partitioning of data when creating/saving/updating a sub model

```php
$address = ShippingAddress::create($request->validated());

$address->getAttributes(); // 'email', 'phone', 'contact_person', 'is_expedited', 'courier'
$address->super->getAttributes(); // 'city', 'country_id', 'street', 'postal_code'
```

Automatically retrieving attributes from the super model

```php
$address->city; // Ghent
$address->super->city; // Ghent
```

Automatically retrieving relationships from the super model

```php
$address->country; // Belgium
$address->super->country; // Belgium
```

Automatically deleting the super model along with the sub model

```php
$address->delete(); // Database transaction in the background
```

## A note on always eager loading the "super" relationship

It does not make sense for the sub model to exist without its complementary data from the super model. By having two tables, we are able to achieve a normalized database, but in code, it only makes sense when they coexist as a whole. 

## Credits

- [Muhammed Sari](https://github.com/mabdullahsari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
