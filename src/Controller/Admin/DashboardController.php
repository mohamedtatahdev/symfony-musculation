<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Equipment;
use App\Entity\Exercice;
use App\Entity\Muscle;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('FlashMuscu');
    }

    
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Catégories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Muscles', 'fas fa-list', Muscle::class);
        yield MenuItem::linkToCrud('Equipements', 'fas fa-list', Equipment::class);
        yield MenuItem::linkToCrud('Exercice', 'fas fa-list', Exercice::class);
    }
}
