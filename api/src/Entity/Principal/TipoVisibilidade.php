<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Helper\SituacoesSistema;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TipoVisibilidadeRepository")
 */
class TipoVisibilidade
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="tipoVisibilidades")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $situacao = SituacoesSistema::SITUACAO_ATIVO;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

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


}
