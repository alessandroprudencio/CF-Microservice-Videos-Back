<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;
use App\Traits\UploadFiles;
use App\Models\Category;
use App\Models\Genre;

class Video extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Uuid;
    use UploadFiles;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    public $incrementing = false;

    public static $fileFields = ['video_file', 'thumb_file'];

    protected $fillable = [
        'name',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'video_file',
        'thumb_file'
    ];

    protected $dates =  ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    protected $attributes = [
        // 'opened' => false,
        'deleted_at' => null
    ];

    protected $keyType = 'string';

    public static function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);
        try {
            \DB::beginTransaction();
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            $obj->uploadFiles($files);
            \DB::commit();
            return $obj;
        } catch (\Exception $exception) {
            if (isset($obj)) {
                $obj->deleteFiles($files);
            }
            \DB::rollback();
            throw $exception;
        }
    }

    public function update(array $attributes = [], array $options = [])
    {
        $files = self::extractFiles($attributes);
        try {
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if ($saved) {
                $this->uploadFiles($files);
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

    protected function uploadDir()
    {
        return $this->id;
    }
}
