<?php

namespace ALT\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ALT\AppBundle\Entity\Billet;

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
     * @ORM\ManyToOne(targetEntity="ALT\AppBundle\Entity\Billet",inversedBy="commentaires", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $billet;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="ALT\AppBundle\Entity\Commentaire",inversedBy="enfants")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parent;
    
    /**
     * @var
     * @ORM\OneToMany(targetEntity="ALT\AppBundle\Entity\Commentaire",mappedBy="parent")
     */
    private $enfants;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $niveau = 0;

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
    private $signale = false;


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
     * @param Billet $billet
     *
     * @return Commentaire
     */
    public function setBillet(Billet $billet)
    {
        $this->billet = $billet;

        return $this;
    }

    /**
     * @return Billet
     *
     */
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

    /**
     * Set niveau
     *
     * @param integer $niveau
     *
     * @return Commentaire
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return integer
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set parent
     *
     * @param \ALT\AppBundle\Entity\Commentaire $parent
     *
     * @return Commentaire
     */
    public function setParent(\ALT\AppBundle\Entity\Commentaire $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \ALT\AppBundle\Entity\Commentaire
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add enfant
     *
     * @param \ALT\AppBundle\Entity\Commentaire $enfant
     *
     * @return Commentaire
     */
    public function addEnfant(\ALT\AppBundle\Entity\Commentaire $enfant)
    {
        $this->enfants[] = $enfant;

        return $this;
    }

    /**
     * Remove enfant
     *
     * @param \ALT\AppBundle\Entity\Commentaire $enfant
     */
    public function removeEnfant(\ALT\AppBundle\Entity\Commentaire $enfant)
    {
        $this->enfants->removeElement($enfant);
    }

    /**
     * Get enfants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnfants()
    {
        return $this->enfants;
    }
}
