<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use App\Models\Category;
use App\Models\Genre;

class Video extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Uuid;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    public $incrementing = false;

    protected $fillable = [
        'name',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration'
    ];

    protected $dates =  ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    protected $attributes = [
        'opened' => false,
        'deleted_at' => null
    ];

    protected $keyType = 'string';

    public static function create(array $attributes = [])
    {
        try {
            \DB::beginTransaction();
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            //uploads
            \DB::commit();
            return $obj;
        } catch (\Exception $exception) {
            if (isset($obj)) {
                //excluir os arquivos
            }
            \DB::rollback();
            throw $exception;
        }
    }

    public function update(array $attributes = [], array $options = [])
    {
        try {
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if ($saved) {
                //uploads de novos
                //excluir os antigos
            }
            \DB::commit();
            return $saved;
        } catch (\Exception $exception) {
            //excluir os arquivos
            \DB::rollback();
            throw $exception;
        }
    }

    public static function handleRelations(Video $video, array $attributes)
    {
        if (isset($attributes['categories_id'])) {
            $video->categories()->sync($attributes['categories_id']);
        }

        if (isset($attributes['genres_id'])) {
            $video->genres()->sync($attributes['genres_id']);
        }
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }
}
