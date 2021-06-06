<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideosSeeder extends Seeder
{

    private $allGenres;
    private $relations = [
        'genres_id' => [],
        'categories_id' => []
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dir = Storage::getDriver()->getAdapter()->getPathPrefix();

        \File::deleteDirectory($dir, true);

        $self = $this;

        $this->allGenres = Genre::all();

        Model::reguard();

        Video::factory()->count(100)->make()->each(function (Video $video) use ($self) {
            $self->fetchRelations();

            Video::create(
                array_merge(
                    $video->toArray(),
                    [
                        'thumb_file' => $self->getImageFile(),
                        'banner_file' => $self->getImageFile(),
                        'trailer_file' => $self->getVideoFile(),
                        'video_file' => $self->getVideoFile(),
                    ],
                    $this->relations
                )
            );
        });

        Model::unguard();
    }

    public function fetchRelations()
    {
        $subGenres = $this->allGenres->random(5)->load('categories');

        $categoriesId = [];

        foreach ($subGenres as $genre) {
            array_push($categoriesId, ...$genre->categories->pluck('id')->toArray());
        }

        $categoriesId = array_unique($categoriesId);

        $genresId = $subGenres->pluck('id')->toArray();

        $this->relations['categories_id'] = $categoriesId;

        $this->relations['genres_id'] = $genresId;
    }

    public function getImageFile()
    {
        return new UploadedFile(
            storage_path('faker/thumbs/Caminhos_da_Memoria_Hugh_Jackman.jpg'),
            'Caminhos_da_Memoria_Hugh_Jackman.jpg'
        );
    }


    public function getVideoFile()
    {
        return new UploadedFile(
            storage_path('faker/videos/CAMINHOS_DA_MEMÓRIA_Trailer.mp4'),
            'CAMINHOS_DA_MEMÓRIA_Trailer.mp4'
        );
    }
}
