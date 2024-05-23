<?php

namespace App\Twig;


use Twig\Extension\GlobalsInterface;
use Twig\Extension\AbstractExtension;
use App\Repository\CategoryRepository;
use App\Repository\EquipmentRepository;
use App\Repository\MuscleRepository;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;
    private $muscleRepository;
    private $equipmentRepository;

    public function __construct(CategoryRepository $categoryRepository,MuscleRepository $muscleRepository, EquipmentRepository $equipmentRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->muscleRepository = $muscleRepository;
        $this->equipmentRepository = $equipmentRepository;
    }


    public function getGlobals(): array
    {
        return [
            'allCategories' => $this->categoryRepository->findAll(), //eviter de recuperer les categories dans tous les controller
            'allMuscles' => $this->muscleRepository->findAll(),
            'allEquipments' => $this->equipmentRepository->findAll(),
        ];
    }



}