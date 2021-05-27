<?php

/**
 * @OA\Schema(
 *     title="GenreResource",
 *     description="Genre resource",
 *     @OA\Xml(
 *         name="GenreResource"
 *     )
 * )
 */
class GenreResource
{

    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * @OA\Property(
     *     title="Data",
     *     description="Data wrapper"
     * )
     *
     * @var \App\Virtual\Models\Genre[]
     */
    private $data;
}