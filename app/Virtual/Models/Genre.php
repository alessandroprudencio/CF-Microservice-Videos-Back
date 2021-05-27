<?php

/**
 * @OA\Schema(
 *     title="Genre",
 *     description="Genre model",
 *     @OA\Xml(
 *         name="Genre"
 *     )
 * )
 */
class Genre
{

      /**
     * @OA\Property(
     *      title="Id",
     *      description="Id",
     *      example="022a36d7-c63e-424f-81bb-4ae48db00900"
     * )
     *
     * @var string
     */
    public $id;

    /**
     * @OA\Property(
     *      title="Name",
     *      description="Name of the new genre",
     *      example="A nice genre"
     * )
     *
     * @var string
     */
    public $name;

    
    /**
     * @OA\Property(
     *      title="Is active",
     *      description="Is active of the new genre",
     *      example="This is genre inactive"
     * )
     *
     * @var boolean
     */
    public $is_active;

    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @OA\Property(
     *     title="Deleted at",
     *     description="Deleted at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $deleted_at;
}