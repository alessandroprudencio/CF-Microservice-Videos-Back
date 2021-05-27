<?php

/**
 * @OA\Schema(
 *     title="Category",
 *     description="Category model",
 *     @OA\Xml(
 *         name="Category"
 *     )
 * )
 */
class Category
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
     *      description="Name of the new category",
     *      example="A nice category"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="Description",
     *      description="Description of the new category",
     *      example="This is new category's description"
     * )
     *
     * @var string
     */
    public $description;

    
    /**
     * @OA\Property(
     *      title="Is active",
     *      description="Is active of the new category",
     *      example="This is category inactive"
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