<?php

namespace App\Twig;


use Twig\Extension\GlobalsInterface;
use Twig\Extension\AbstractExtension;
use App\Repository\CategoryRepository;
use App\Repository\MuscleRepository;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;
    private $muscleRepository;

    public function __construct(CategoryRepository $categoryRepository,MuscleRepository $muscleRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->muscleRepository = $muscleRepository;
    }


    public function getGlobals(): array
    {
        return [
            'allCategories' => $this->categoryRepository->findAll(), //eviter de recuperer les categories dans tous les controller
            'allMuscles' => $this->muscleRepository->findAll(),
        ];
    }



}