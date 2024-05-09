<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliotecaClassificacao
 *
 * @ORM\Table(name="biblioteca_classificacao",                                                indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\BibliotecaClassificacaoRepository")
 */
class BibliotecaClassificacao
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $biblioteca_obra_nome;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $descricao;

    /**
     *
     * @var \App\Entity\Importacao\BibliotecaObra
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\BibliotecaObra")
     * @ORM\JoinColumn(nullable=true,                                       onDelete="SET NULL")
     */
    private $biblioteca_obra;

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

    public function getBibliotecaObraNome(): ?string
    {
        return $this->biblioteca_obra_nome;
    }

    public function setBibliotecaObraNome(?string $biblioteca_obra_nome): self
    {
        $this->biblioteca_obra_nome = $biblioteca_obra_nome;

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

    public function getBibliotecaObra(): ?BibliotecaObra
    {
        return $this->biblioteca_obra;
    }

    public function setBibliotecaObra(?BibliotecaObra $biblioteca_obra): self
    {
        $this->biblioteca_obra = $biblioteca_obra;

        return $this;
    }


}
