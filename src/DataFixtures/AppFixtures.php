<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Vote;
use App\Entity\Muscle;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\Exercice;
use App\Entity\Equipment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer les catégories
        $categoriesData = [
            ['name' => 'haut du corps', 'slug' => 'haut-du-corps'],
            ['name' => 'bas du corps', 'slug' => 'bas-du-corps'],
        ];

        $categories = [];

        foreach ($categoriesData as $catData) {
            $category = new Category();
            $category->setName($catData['name']);
            $category->setSlug($catData['slug']);
            $manager->persist($category);
            $categories[$catData['name']] = $category; // Stocker la catégorie par nom
        }

        // Créer les équipements fictifs
        $equipments = [];
        for ($i = 0; $i < 10; $i++) {
            $equipment = new Equipment();
            $name = $faker->word;
            $slug = $faker->slug;

            $equipment->setName($name);
            $equipment->setSlug($slug);

            $manager->persist($equipment);
            $equipments[] = $equipment;
        }

        // Créer les muscles pour "haut du corps"
        $muscles = [];
        for ($i = 0; $i < 6; $i++) {
            $muscle = new Muscle();
            $muscle->setName($faker->word);
            $muscle->setSlug($faker->slug);
            $muscle->setPicture('2024-05-14-1a5dab325a56b892866ab7f6f7240ce2ecf05c7c.png');
            $muscle->setCategory($categories['haut du corps']);
            $manager->persist($muscle);
            $muscles[] = $muscle;
        }

        // Créer les muscles pour "bas du corps"
        for ($i = 0; $i < 4; $i++) {
            $muscle = new Muscle();
            $muscle->setName($faker->word);
            $muscle->setSlug($faker->slug);
            $muscle->setPicture('2024-05-14-1a5dab325a56b892866ab7f6f7240ce2ecf05c7c.png');
            $muscle->setCategory($categories['bas du corps']);
            $manager->persist($muscle);
            $muscles[] = $muscle;
        }

        // Créer des utilisateurs fictifs
        $users = [];
        $adminUsers = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);

            // Hash le mot de passe
            $password = $this->passwordHasher->hashPassword($user, 'password123');
            $user->setPassword($password);

            // Définir les rôles
            if ($i < 2) {
                $user->setRoles(['ROLE_ADMIN']); // Les deux premiers utilisateurs seront administrateurs
                $adminUsers[] = $user; // Ajouter aux utilisateurs administrateurs
            } else {
                $user->setRoles(['ROLE_USER']); // Les autres utilisateurs auront le rôle utilisateur par défaut
            }

            $manager->persist($user);
            $users[] = $user;
        }

        // Créer des exercices fictifs
        for ($i = 0; $i < 150; $i++) {
            $exercice = new Exercice();
            $exercice->setName($faker->word);
            $exercice->setSlug($faker->slug);
            $exercice->setDescription($faker->paragraph);
            $exercice->setPicture('2024-05-16-c2e10c65c15108bd6c8932fa93ec60e489f84d1b.webp');
            $exercice->setRating($faker->numberBetween(-5, 5));

            // Associer aléatoirement des muscles et équipements aux exercices
            $exercice->setMuscle($faker->randomElement($muscles));
            $exercice->setEquipment($faker->randomElement($equipments));
            // Associer l'exercice à un utilisateur administrateur
            $exercice->setUser($faker->randomElement($adminUsers));

            $manager->persist($exercice);
            $exercices[] = $exercice;

        }

            foreach ($exercices as $exercice) {
            $commentCount = $faker->numberBetween(1, 10); // Générer entre 1 et 10 commentaires
            for ($j = 0; $j < $commentCount; $j++) {
                $comment = new Comment();
                $comment->setContent($faker->sentence);
                $comment->setCreatedAt(new \DateTimeImmutable());
                // Associer aléatoirement des utilisateurs aux commentaires
                $comment->setUser($faker->randomElement($users));
                $comment->setExercice($exercice);

                $manager->persist($comment);
            }
        }

        foreach ($exercices as $exercice) {
            $voteCount = $faker->numberBetween(1, 20); // Générer entre 1 et 20 votes
            for ($j = 0; $j < $voteCount; $j++) {
                $vote = new Vote();
                $vote->setUser($faker->randomElement($users));
                $vote->setExercice($exercice);
                $vote->setIsLiked($faker->boolean); // Générer un vote "like" ou "dislike"

                $manager->persist($vote);
            }
        }

        foreach ($exercices as $exercice) {
            $usersWhoVoted = []; // Initialiser un tableau pour suivre les utilisateurs qui ont voté pour cet exercice
            $voteCount = $faker->numberBetween(1, 10); // Générer entre 1 et 10 votes par exercice
            for ($j = 0; $j < $voteCount; $j++) {
                $user = $faker->randomElement($users);
                
                // Assurez-vous que cet utilisateur n'a pas déjà voté pour cet exercice
                if (!in_array($user->getId(), $usersWhoVoted)) {
                    $vote = new Vote();
                    $vote->setUser($user);
                    $vote->setExercice($exercice);
                    $vote->setIsLiked($faker->boolean); // Générer un vote "like" ou "dislike"
                    
                    $manager->persist($vote);
                    
                    // Ajouter cet utilisateur à la liste des utilisateurs qui ont voté pour cet exercice
                    $usersWhoVoted[] = $user->getId();
                }
            }
        }
                $manager->flush();
    }
}
