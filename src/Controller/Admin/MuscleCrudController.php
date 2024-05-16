<?php

namespace App\Controller\Admin;

use App\Entity\Muscle;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MuscleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Muscle::class;
    }

       //configure le titrage
       public function configureCrud(Crud $crud): Crud
       {
           return $crud
            ->setEntityLabelInSingular('Muscle')
            ->setEntityLabelInPlural('Muscles')
           ;
       }
   
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom')->setHelp('Nom du muscle'),
            SlugField::new('slug')->setLabel('Url')->setTargetFieldName('name')->setHelp('Url du muscle générer automatiquement'),
            ImageField::new('picture')
            ->setLabel('Image')
            ->setHelp('L\'image de votre produit')
            ->setUploadDir('/public/uploads/muscles')
            ->setBasePath('/uploads/muscles')
            ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]'),
            AssociationField::new('category')
            ->setLabel('Catégorie')
            ->setHelp('Catégorie associée'),
        ];
    }
    
}
