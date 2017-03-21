<?php

namespace ALT\AppBundle\Repository;
use ALT\AppBundle\Entity\Billet;

/**
 * CommentaireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentaireRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Création du querybuilder pour l'entité "Commentaire"
     * On veut récupérer le  nombre de commentaires via la fonction COUNT()
     *
     * Si on a un filtre ....
     * 
     * On retourne le résultat du comptage 
     *
     * @param null $filtre
     * @param Billet|null $billet
     * @return mixed
     * @throws \Exception
     */
    public function countNbCommentaires($filtre = null, Billet $billet = null)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('COUNT(c.id)');

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
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Création du querybuilder pour l'entité "Commentaire"
     * On veut récupérer le  nombre de commentaire via la fonction COUNT()
     * et sélectionner seulement les commentaire où signale = 1
     *
     * On retourne le résultat du comptage
     * 
     * @return mixed
     */
    public function countNbCommentairesSignales()
    {
        $qb = $this->createQueryBuilder('c'); 
        $qb
            ->andWhere($qb->expr()->in('c.signale', 1))
            ->select('COUNT(c.id)'); 

        return $qb->getQuery()->getSingleScalarResult(); 
    }

    /**
     * Création du querybuilder pour l'entité "Commentaire"
     * pour récupérer des commentaires depuis notre base de données, triés par "id" en ordre descendant.
     * 
     * Si on a un filtre ....
     * 
     * On retourne le résultat 
     * 
     * @param null $filtre
     * @param null $billet
     * @return array
     * @throws \Exception

    public function listeCommentaires($filtre = null, $billet = null)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->orderBy('c.id', 'desc');

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
     * */


    /**
     * $nbCommentairess vaut la méthode countNbCommentaires
     *
     * Compte le nombre de pages totales (ceil() fait l'arrondie à l'entier supérieur)
     *
     * Calcul du point de départ pour récupérer les enregistrements en base de données
     *
     * On creer les conditions (filtre / billet)
     *
     * la méthode "findBy" pour récupérer les commentaires depuis notre base de données,
     * Avec pour contrainte les commentaires filtre et billet
     * triés par "id" en ordre descendant
     * avec en paramètre une limite de $commentairesParPage en partant de $offset
     *
     * On retourne le résultat
     *
     * @param $page
     * @param $commentairesParPage
     * @param null $filtre
     * @param Billet|null $billet
     * @return array
     * @throws \Exception
     */
    public function paginerCommentaires($page, $commentairesParPage, $filtre = null, Billet $billet = null)
    {
        $nbCommentaire = $this->countNbCommentaires($filtre, $billet);

        $pagesTotal = (int)ceil($nbCommentaire / $commentairesParPage);

        if ($page > $pagesTotal) {
            throw new \Exception("Page > à nombre totale de pages");
        }

        $offset = ($page - 1) * $commentairesParPage;

        $conditions = array();
        if (isset($billet)) {
            $conditions['billet'] = $billet->getId();
        }

        if (isset($filtre)) {
            switch ($filtre) {
                case 'signaler':
                    $conditions['signale'] = 1;
                    break;
            }
        }

        $listeCommentaires = $this->findBy($conditions, array("date" => "desc"), $commentairesParPage, $offset);

        return [
            'commentaires' => $listeCommentaires,
            'pagesTotal' => $pagesTotal
        ];
    }
}
