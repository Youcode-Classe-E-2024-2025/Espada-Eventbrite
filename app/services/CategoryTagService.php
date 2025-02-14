<?php

namespace App\services;

use App\repository\CategoryRepo;
use App\repository\TagRepo;

class CategoryTagService
{
    private CategoryRepo $categoryRepository;
    private TagRepo $tagRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepo();
        $this->tagRepository = new TagRepo();
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
        return $this->tagRepository->create(['title' => $title]);
    }
}
