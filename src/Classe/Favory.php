<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Favory
{
    //permet de chercher la session
    public function __construct(private RequestStack $requestStack)
    {
            
    }

    public function add($exercice)
    {
     //appeler la session de symfony
$favory = $this->requestStack->getSession()->get('favory')   ;   

        //ajouter un favoris

        $favory[$exercice->getId()] = $exercice;
        
      
    //cree ma session favoris

        $this->requestStack->getSession()->set('favory', $favory);

 
    }

    public function delete($id)
    {
        $favory = $this->requestStack->getSession()->get('favory'); 
        
        unset($favory[$id]) ;

        $this->requestStack->getSession()->set('favory', $favory); // retourne la session favory
    }


    public function getFavory()
    {
        return $this->requestStack->getSession()->get('favory'); // retourne la session favory
    }
   
   
}