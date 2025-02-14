<?php

namespace App\services;

use App\repository\CategoryRepository;
use App\repository\TagRepository;

class CategoryTagService
{
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->tagRepository = new TagRepository();
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->getAll();
    }

    public function getAllTAgs()
    {
        return $this->tagRepository->getAll();
    }

    public function addTag($title)
    {
        return $this->tagRepository->create($title);
    }

    public function addCategory($title, $icon)
    {
        return $this->categoryRepository->create($title, $icon);
    }

    public function deleteCategory($id)
    {
        return $this->categoryRepository->delete($id);
    }

    public function deleteTag($id)
    {
        return $this->tagRepository->delete($id);
    }
}
