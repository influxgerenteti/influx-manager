<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliotecaExemplares
 *
 * @ORM\Table(name="biblioteca_exemplares",                                                indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\BibliotecaExemplaresRepository")
 */
class BibliotecaExemplares
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
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $numero_exemplares;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $ano;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $data_aquisicao;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=2, nullable=true, options={"fixed"=true,"comment"="A - Ativo, I - Inativo"})
     */
    private $situacao;

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
    private $edicao;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $volume;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $codigo_barra;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $tombo;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $localizacao;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $isbn;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $issn;

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

    public function getNumeroExemplares(): ?string
    {
        return $this->numero_exemplares;
    }

    public function setNumeroExemplares(?string $numero_exemplares): self
    {
        $this->numero_exemplares = $numero_exemplares;

        return $this;
    }

    public function getAno(): ?string
    {
        return $this->ano;
    }

    public function setAno(?string $ano): self
    {
        $this->ano = $ano;

        return $this;
    }

    public function getDataAquisicao(): ?string
    {
        return $this->data_aquisicao;
    }

    public function setDataAquisicao(?string $data_aquisicao): self
    {
        $this->data_aquisicao = $data_aquisicao;

        return $this;
    }

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(?string $situacao): self
    {
        $this->situacao = $situacao;

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

    public function getEdicao(): ?string
    {
        return $this->edicao;
    }

    public function setEdicao(?string $edicao): self
    {
        $this->edicao = $edicao;

        return $this;
    }

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(?string $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getCodigoBarra(): ?string
    {
        return $this->codigo_barra;
    }

    public function setCodigoBarra(?string $codigo_barra): self
    {
        $this->codigo_barra = $codigo_barra;

        return $this;
    }

    public function getTombo(): ?string
    {
        return $this->tombo;
    }

    public function setTombo(?string $tombo): self
    {
        $this->tombo = $tombo;

        return $this;
    }

    public function getLocalizacao(): ?string
    {
        return $this->localizacao;
    }

    public function setLocalizacao(?string $localizacao): self
    {
        $this->localizacao = $localizacao;

        return $this;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    public function setObservacao($observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getIssn(): ?string
    {
        return $this->issn;
    }

    public function setIssn(?string $issn): self
    {
        $this->issn = $issn;

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
