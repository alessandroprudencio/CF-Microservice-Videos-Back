<?php

/**
 * @OA\Schema(
 *      title="Store Project request",
 *      description="Store Project request body data",
 *      type="object",
 *      required={"name"}
 * )
 */

class StoreCategoryRequest
{   

    /**
     * @OA\Property(
     *      title="name",
     *      description="Name of the new category",
     *      example="Action"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="description",
     *      description="Description of the new category",
     *      example="This is new category description"
     * )
     *
     * @var string
     */
    public $description;

    
    /**
     * @OA\Property(
     *      title="Is active",
     *      description="Is active of the new category",
     *      example=true
     * )
     *
     * @var boolean
     */
    public $is_active;
}