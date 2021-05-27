<?php

/**
 * @OA\Schema(
 *      title="Store Genre request",
 *      description="Store Genre request body data",
 *      type="object",
 *      required={"name"}
 * )
 */

class StoreGenreRequest
{   

    /**
     * @OA\Property(
     *      title="name",
     *      description="Name of the new genre",
     *      example="Action"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="Is active",
     *      description="Is active of the new genre",
     *      example=true
     * )
     *
     * @var boolean
     */
    public $is_active;
}