<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sala
 *
 * @ORM\Table(name="sala",                                                 indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\SalaRepository")
 */
class Sala
{
    /**
     *
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $franqueada_id;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $sigla;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descricao;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $vagas;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=1, nullable=true, options={"fixed"=true,"comment"="S - Sim, N - Não"})
     */
    private $aula_livre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranqueadaId(): ?int
    {
        return $this->franqueada_id;
    }

    public function setFranqueadaId(int $franqueada_id): self
    {
        $this->franqueada_id = $franqueada_id;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getSigla(): ?string
    {
        return $this->sigla;
    }

    public function setSigla(?string $sigla): self
    {
        $this->sigla = $sigla;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getVagas(): ?string
    {
        return $this->vagas;
    }

    public function setVagas(?string $vagas): self
    {
        $this->vagas = $vagas;

        return $this;
    }

    public function getAulaLivre(): ?string
    {
        return $this->aula_livre;
    }

    public function setAulaLivre(?string $aula_livre): self
    {
        $this->aula_livre = $aula_livre;

        return $this;
    }


}
