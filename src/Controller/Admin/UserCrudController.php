<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User)
        {
            $plaintextPassword = $entityInstance->getPassword();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $entityInstance,
                $plaintextPassword,
            );
            $entityInstance->setPassword($hashedPassword);
        }
    
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User && !empty($entityInstance->getPassword()))
        {
            $plaintextPassword = $entityInstance->getPassword();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $entityInstance,
                $plaintextPassword,
            );
            $entityInstance->setPassword($hashedPassword);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = ['ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_USER' => 'ROLE_USER'];

        return [
            TextField::new('email'),
            TextField::new('password'),
            ChoiceField::new('roles')
                ->setChoices($roles)
                ->allowMultipleChoices() // Permettre la sélection de plusieurs rôles si nécessaire
                ->renderAsBadges() // Optionnel: pour un affichage plus visuel
                ->setRequired(true),
            // ... autres champs ...
        ];
    }
}
