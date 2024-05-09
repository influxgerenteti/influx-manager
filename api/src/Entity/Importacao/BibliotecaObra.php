<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliotecaObra
 *
 * @ORM\Table(name="biblioteca_obra",                                                indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"}), @ORM\Index(name="IDX_BIBLIOTECA_EDITORA", columns={"biblioteca_editora_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\BibliotecaObraRepository")
 */
class BibliotecaObra
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nome;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $genero;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $biblioteca_editora_nome;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $idioma;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nivel;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $palavra_chave;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $cdd;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $cutter;

    /**
     *
     * @var \App\Entity\Importacao\BibliotecaEditora
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Importacao\BibliotecaEditora")
     * @ORM\JoinColumn(nullable=true,                                          onDelete="SET NULL")
     */
    private $biblioteca_editora;

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

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getGenero(): ?string
    {
        return $this->genero;
    }

    public function setGenero(?string $genero): self
    {
        $this->genero = $genero;

        return $this;
    }

    public function getBibliotecaEditoraNome(): ?string
    {
        return $this->biblioteca_editora_nome;
    }

    public function setBibliotecaEditoraNome(?string $biblioteca_editora_nome): self
    {
        $this->biblioteca_editora_nome = $biblioteca_editora_nome;

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

    public function getNivel(): ?string
    {
        return $this->nivel;
    }

    public function setNivel(?string $nivel): self
    {
        $this->nivel = $nivel;

        return $this;
    }

    public function getPalavraChave(): ?string
    {
        return $this->palavra_chave;
    }

    public function setPalavraChave(?string $palavra_chave): self
    {
        $this->palavra_chave = $palavra_chave;

        return $this;
    }

    public function getCdd(): ?string
    {
        return $this->cdd;
    }

    public function setCdd(?string $cdd): self
    {
        $this->cdd = $cdd;

        return $this;
    }

    public function getCutter(): ?string
    {
        return $this->cutter;
    }

    public function setCutter(?string $cutter): self
    {
        $this->cutter = $cutter;

        return $this;
    }

    public function getBibliotecaEditora(): ?BibliotecaEditora
    {
        return $this->biblioteca_editora;
    }

    public function setBibliotecaEditora(?BibliotecaEditora $biblioteca_editora): self
    {
        $this->biblioteca_editora = $biblioteca_editora;

        return $this;
    }


}
