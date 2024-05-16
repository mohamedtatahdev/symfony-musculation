<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    //configure le titrage
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
         ->setEntityLabelInSingular('Catégorie')
         ->setEntityLabelInPlural('Catégories')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom')->setHelp('Nom de la catégorie'),
            SlugField::new('slug')->setLabel('Url')->setTargetFieldName('name')->setHelp('Url de la catégorie générer automatiquement')
        ];
    }
}
