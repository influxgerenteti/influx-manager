<?php

namespace App\Entity\Importacao;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Estagio
 *
 * @ORM\Table(name="estagio",                                                 indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"}), @ORM\Index(name="IDX_CURSO", columns={"curso_id"}), @ORM\Index(name="IDX_ITEM", columns={"item_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\EstagioRepository")
 */
class Estagio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="franqueada_id", type="integer", nullable=false)
     */
    private $franqueada_id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="curso_nome", type="string", length=255, nullable=true)
     */
    private $curso_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="item_nome", type="string", length=255, nullable=true)
     */
    private $item_nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="idioma", type="string", length=20, nullable=true)
     */
    private $idioma;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descricao", type="string", length=255, nullable=true)
     */
    private $descricao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome", type="string", length=255, nullable=true)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="maximo_alunos", type="string", length=10, nullable=true)
     */
    private $maximo_alunos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="idade_minima", type="string", length=10, nullable=true)
     */
    private $idade_minima;

    /**
     * @var string|null
     *
     * @ORM\Column(name="idade_maxima", type="string", length=10, nullable=true)
     */
    private $idade_maxima;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ponto_equilibrio", type="string", length=10, nullable=true)
     */
    private $ponto_equilibrio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numero_horas", type="string", length=10, nullable=true)
     */
    private $numero_horas;

    /**
     * @var string|null
     *
     * @ORM\Column(name="valor_hora_aula", type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_hora_aula;

    /**
     * @var string|null
     *
     * @ORM\Column(name="situacao", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="A - Ativo, I - Inativo"})
     */
    private $situacao;

    /**
     * @var \App\Entity\Importacao\Curso
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Curso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="curso_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $curso;

    /**
     * @var \App\Entity\Importacao\Item
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\Item")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id",                            referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $item;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\Contrato", mappedBy="estagio")
     */
    private $contratos;

    public function __construct()
    {
        $this->contratos = new ArrayCollection();
    }

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

    public function getCursoNome(): ?string
    {
        return $this->curso_nome;
    }

    public function setCursoNome(?string $curso_nome): self
    {
        $this->curso_nome = $curso_nome;

        return $this;
    }

    public function getItemNome(): ?string
    {
        return $this->item_nome;
    }

    public function setItemNome(?string $item_nome): self
    {
        $this->item_nome = $item_nome;

        return $this;
    }

    public function getIdioma(): ?string
    {
        return $this->idioma;
    }

    public function setIdioma(?string $idioma): self
    {
        $this->idioma = $idioma;

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

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getMaximoAlunos(): ?string
    {
        return $this->maximo_alunos;
    }

    public function setMaximoAlunos(?string $maximo_alunos): self
    {
        $this->maximo_alunos = $maximo_alunos;

        return $this;
    }

    public function getIdadeMinima(): ?string
    {
        return $this->idade_minima;
    }

    public function setIdadeMinima(?string $idade_minima): self
    {
        $this->idade_minima = $idade_minima;

        return $this;
    }

    public function getIdadeMaxima(): ?string
    {
        return $this->idade_maxima;
    }

    public function setIdadeMaxima(?string $idade_maxima): self
    {
        $this->idade_maxima = $idade_maxima;

        return $this;
    }

    public function getPontoEquilibrio(): ?string
    {
        return $this->ponto_equilibrio;
    }

    public function setPontoEquilibrio(?string $ponto_equilibrio): self
    {
        $this->ponto_equilibrio = $ponto_equilibrio;

        return $this;
    }

    public function getNumeroHoras(): ?string
    {
        return $this->numero_horas;
    }

    public function setNumeroHoras(?string $numero_horas): self
    {
        $this->numero_horas = $numero_horas;

        return $this;
    }

    public function getValorHoraAula()
    {
        return $this->valor_hora_aula;
    }

    public function setValorHoraAula($valor_hora_aula): self
    {
        $this->valor_hora_aula = $valor_hora_aula;

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

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): self
    {
        $this->curso = $curso;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return Collection|Contrato[]
     */
    public function getContratos(): Collection
    {
        return $this->contratos;
    }

    public function addContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === false) {
            $this->contratos[] = $contrato;
            $contrato->setEstagio($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getEstagio() === $this) {
                $contrato->setEstagio(null);
            }
        }

        return $this;
    }


}
