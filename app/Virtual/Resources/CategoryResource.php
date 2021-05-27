<?php

/**
 * @OA\Schema(
 *     title="CategoryResource",
 *     description="Category resource",
 *     @OA\Xml(
 *         name="CategoryResource"
 *     )
 * )
 */
class CategoryResource
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
     * @var \App\Virtual\Models\Category[]
     */
    private $data;
}