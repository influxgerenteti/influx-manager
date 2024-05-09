<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\NivelInstrutorRepository")
 * @ORM\Table(name="nivel_instrutor")
 */
class NivelInstrutor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"A - Ativo, I - Inativo"})
     */
    private $situacao = 'A';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ValorHora", mappedBy="nivel_instrutor")
     */
    private $valorHoras;

    /**
     * @ORM\Column(type="integer")
     */
    private $ano;

    public function __construct()
    {
        $this->valorHoras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    /**
     * @return Collection|ValorHora[]
     */
    public function getValorHoras(): Collection
    {
        return $this->valorHoras;
    }

    public function getAno(): ?int
    {
        return $this->ano;
    }

    public function setAno(int $ano): self
    {
        $this->ano = $ano;

        return $this;
    }


}
