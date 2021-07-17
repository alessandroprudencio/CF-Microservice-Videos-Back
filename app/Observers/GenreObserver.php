<?php

namespace App\Observers;

use App\Models\Genre;
use Bschmitt\Amqp\Message;

class GenreObserver
{
    /**
     * Handle the Genre "created" event.
     *
     * @param  \App\Models\Genre  $genre
     * @return void
     */
    public function created(Genre $genre)
    {
        $message = new Message(
            $genre->toJson()
        );

        \Amqp::publish('model.genre.created', $message);
    }

    /**
     * Handle the Genre "updated" event.
     *
     * @param  \App\Models\Category  $genre
     * @return void
     */
    public function updated(Genre $genre)
    {
        $message = new Message($genre->toJson());

        \Amqp::publish('model.genre.updated', $message);
    }

    /**
     * Handle the Genre "deleted" event.
     *
     * @param  \App\Models\Genre  $genre
     * @return void
     */
    public function deleted(Genre $genre)
    {
        $message = new Message(json_encode(['id'=>$genre->id]));

        Amqp::publish('model.genre.deleted', $message);
    }

    /**
     * Handle the Genre "restored" event.
     *
     * @param  \App\Models\Genre  $genre
     * @return void
     */
    public function restored(Genre $genre)
    {
        //
    }

    /**
     * Handle the Genre "force deleted" event.
     *
     * @param  \App\Models\Genre  $genre
     * @return void
     */
    public function forceDeleted(Genre $genre)
    {
        //
    }
}
