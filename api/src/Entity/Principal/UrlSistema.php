<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\UrlSistemaRepository")
 * @ORM\Table(name="url_sistema")
 */
class UrlSistema
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url_sistema;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Modulo", inversedBy="urlSistemas", fetch="LAZY")
     */
    private $modulos;

    public function __construct()
    {
        $this->modulos = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUrlSistema(): ?string
    {
        return $this->url_sistema;
    }

    public function setUrlSistema(string $url_sistema): self
    {
        $this->url_sistema = $url_sistema;

        return $this;
    }

    /**
     * @return Collection|Modulo[]
     */
    public function getModulos(): Collection
    {
        return $this->modulos;
    }

    public function addModulo(Modulo $modulo): self
    {
        if ($this->modulos->contains($modulo) === false) {
            $this->modulos[] = $modulo;
        }

        return $this;
    }

    public function removeModulo(Modulo $modulo): self
    {
        if ($this->modulos->contains($modulo) === true) {
            $this->modulos->removeElement($modulo);
        }

        return $this;
    }


}
