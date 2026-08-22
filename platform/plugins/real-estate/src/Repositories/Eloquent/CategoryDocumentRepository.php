<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Repositories\Interfaces\CategoryDocumentInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class CategoryDocumentRepository extends RepositoriesAbstract implements CategoryDocumentInterface
{
    public function getByCategoryId($categoryId)
    {
        return $this->model->where('category_id', $categoryId)->count();
    }
}