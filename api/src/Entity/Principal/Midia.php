<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MidiaRepository")
 */
class Midia
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
    private $descricao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="midias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="string", length=4, options={"comment"="MON - Midia Online, MOF - Midia Offline, MLOC - Midia Local"})
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=1, options={"comment"="A - Ativo, I - Inativo"})
     */
    private $situacao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MidiaFranqueada", mappedBy="midia")
     */
    private $midiaFranqueadas;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Aluno", mappedBy="tipo_visibilidade")
     */
    private $alunos;

    public function __construct()
    {
        $this->midiaFranqueadas = new ArrayCollection();
        $this->alunos           = new ArrayCollection();
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

    public function getFranqueada(): ?Franqueada
    {
        return $this->franqueada;
    }

    public function setFranqueada(?Franqueada $franqueada): self
    {
        $this->franqueada = $franqueada;

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

    /**
     * @return Collection|MidiaFranqueada[]
     */
    public function getMidiaFranqueadas(): Collection
    {
        return $this->midiaFranqueadas;
    }

    public function addMidiaFranqueada(MidiaFranqueada $midiaFranqueada): self
    {
        if ($this->midiaFranqueadas->contains($midiaFranqueada) === false) {
            $this->midiaFranqueadas[] = $midiaFranqueada;
            $midiaFranqueada->setMidia($this);
        }

        return $this;
    }

    public function removeMidiaFranqueada(MidiaFranqueada $midiaFranqueada): self
    {
        if ($this->midiaFranqueadas->contains($midiaFranqueada) === true) {
            $this->midiaFranqueadas->removeElement($midiaFranqueada);
            // set the owning side to null (unless already changed)
            if ($midiaFranqueada->getMidia() === $this) {
                $midiaFranqueada->setMidia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Aluno[]
     */
    public function getAlunos(): Collection
    {
        return $this->alunos;
    }

    public function addAluno(Aluno $aluno): self
    {
        if ($this->alunos->contains($aluno) === false) {
            $this->alunos[] = $aluno;
            $aluno->addTipoVisibilidade($this);
        }

        return $this;
    }

    public function removeAluno(Aluno $aluno): self
    {
        if ($this->alunos->contains($aluno) === true) {
            $this->alunos->removeElement($aluno);
            $aluno->removeTipoVisibilidade($this);
        }

        return $this;
    }


}
