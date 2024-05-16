<?php

namespace App\Controller\Admin;

use App\Entity\Exercice;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ExerciceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Exercice::class;
    }

           //configure le titrage
           public function configureCrud(Crud $crud): Crud
           {
               return $crud
                ->setEntityLabelInSingular('Exercice')
                ->setEntityLabelInPlural('Exercices')
               ;
           }

    
           public function configureFields(string $pageName): iterable
           {
               return [
                   TextField::new('name')->setLabel('Nom')->setHelp('Nom de l\'exercice'),
                   SlugField::new('slug')->setLabel('Url')->setTargetFieldName('name')->setHelp('Url du muscle générer automatiquement'),
                   ImageField::new('picture')
                   ->setLabel('Image')
                   ->setHelp('L\'image de votre produit')
                   ->setUploadDir('/public/uploads/exercices')
                   ->setBasePath('/uploads/exercices')
                   ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]'),
                   TextareaField::new('description')->setLabel('Description')->setHelp('Description de l\'exercice'),
                   AssociationField::new('equipment')
                   ->setLabel('Equipement')
                   ->setHelp('Equipement associée'),
                   AssociationField::new('muscle')
                   ->setLabel('Muscle')
                   ->setHelp('Muscle associée'),
               ];
           }
    
}
