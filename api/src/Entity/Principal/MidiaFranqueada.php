<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MidiaFranqueadaRepository")
 */
class MidiaFranqueada
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Midia", inversedBy="midiaFranqueadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $midia;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="midiaFranqueadas")
     */
    private $franqueada;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visibilidade;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMidia(): ?Midia
    {
        return $this->midia;
    }

    public function setMidia(?Midia $midia): self
    {
        $this->midia = $midia;

        return $this;
    }

    public function getFranqueada(): ?Franqueada
    {
        return $this->franqueada;
    }

    public function setFranqueada(?Franqueada $franqueada): self
    {
        $this->franqueada = $franqueada;

        return $this;
    }

    public function getVisibilidade(): ?bool
    {
        return $this->visibilidade;
    }

    public function setVisibilidade(bool $visibilidade): self
    {
        $this->visibilidade = $visibilidade;

        return $this;
    }


}
