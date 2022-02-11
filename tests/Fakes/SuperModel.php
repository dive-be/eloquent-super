<?php declare(strict_types=1);

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;

class SuperModel extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
    ];

    protected $table = 'test_models_super';

    public function aRandomMethod(): string
    {
        return 'Lorem';
    }
}
