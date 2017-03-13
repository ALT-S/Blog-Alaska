<?php

namespace ALT\AppBundle\Repository;

/**
 * CommentaireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentaireRepository extends \Doctrine\ORM\EntityRepository
{
    function countNbCommentaires()
    {
        $qb = $this->createQueryBuilder('c'); // Création du querybuilder pour l'entité "Commentaire"
        $qb->select('COUNT(c.id)'); // On veut récupérer le  nombre de billets via la fonction COUNT()

        return $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans $nbCommentaires 
    }

    function countNbCommentairesSignales()
    {
        $qb = $this->createQueryBuilder('c'); // Création du querybuilder pour l'entité "Commentaire"
        $qb
            ->andWhere($qb->expr()->in('c.signale', 1))// ->andWhere('c.signale = 1')
            ->select('COUNT(c.id)'); // On veut récupérer le nombre de commentaires via la fonction COUNT()

        return $qb->getQuery()->getSingleScalarResult(); // On récupère le résultat du comptage dans  $nbCommentairesSignales
    }
    
    public function listeCommentaires($filtre = null, $billet = null)
    {
        $qb = $this->createQueryBuilder('c');// Création du querybuilder pour l'entité "Commentaire"
        $qb->orderBy('c.id', 'desc');// pour récupérer des commentaires depuis notre base de données, triés par "id" en ordre descendant.

        if (isset($filtre)) { // Si on a un filtre dans l'URL
            switch ($filtre) { // On regarde quel filtre on demande :
                case 'signaler': // Si on ne veut que les commentaires "signalés"
                    $qb->andWhere('c.signale = 1');
                    break;
                case 'billet': // Si on ne veut que les commentaires d'un billet en particulier
                    if (!isset($billet)) {
                        throw new \Exception("Billet obligatoire pour la recherche via un billet spécifique");
                    }

                    $qb->andWhere('c.billet = :billet'); // On attache le billet à la condition pour récupérer les commentaires
                    $qb->setParameter('billet', $billet);
                    break;
            }
        }

        return $qb->getQuery()->getResult();
    }
    
    
}
