<?php

namespace ALT\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire")
 * @ORM\Entity(repositoryClass="ALT\AppBundle\Repository\CommentaireRepository")
 * @ORM\HasLifecycleCallbacks()
 */

class Commentaire
{
    /**
     * @ORM\ManyToOne(targetEntity="ALT\AppBundle\Entity\Billet",inversedBy="commentaires", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $billet;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="auteur", type="string", length=255)
     */
    private $auteur;


    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @ORM\Column(name="signale", type="boolean")
     */
    private $signale;


    //constructeur pour définir une date par défaut
    public function __construct()
    {
        $this->date = new \Datetime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Commentaire
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set auteur
     *
     * @param string $auteur
     *
     * @return Commentaire
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Commentaire
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set billet
     *
     * @param \ALT\AppBundle\Entity\billet $billet
     *
     * @return Commentaire
     */
    public function setBillet(\ALT\AppBundle\Entity\billet $billet)
    {
        $this->billet = $billet;

        return $this;
    }

    public function getBillet()
    {
        return $this->billet;
    }

    public function setSignale($signale)
    {
        $this->signale = $signale;

        return $this;
    }


    public function getSignale()
    {
        return $this->signale;
    }
}
