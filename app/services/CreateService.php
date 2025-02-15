<?php

namespace App\services;

use App\repository\TagRepository;


use App\repository\CategoryRepository;

class CreateService {
    private TagRepository $tagRepo;
    private CategoryRepository $categoRepo;

    public function __construct() {
        $this->tagRepo = new TagRepository();
        $this->categoRepo = new CategoryRepository();
    }

    

    public function getAllTagAndCatego(): array {

        $data = [];

        $data['tags'] = $this->tagRepo->getAll();
        $data['categos'] = $this->categoRepo->getAll();

        return $data;

    }










}











?>