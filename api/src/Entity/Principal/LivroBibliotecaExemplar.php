<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\LivroBibliotecaExemplarRepository")
 */
class LivroBibliotecaExemplar
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\LivroBiblioteca", inversedBy="livroBibliotecaExemplars")
     * @ORM\JoinColumn(nullable=false)
     * (label="Livro", showOnBrowse="true", showOnCreate="true", showOnUpdate="true", formViewOrder="2", descriptionColumn="nome")
     */
    private $livro_biblioteca;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\EmprestimoBiblioteca", mappedBy="livro_biblioteca_exemplar")
     */
    private $emprestimoBibliotecas;

    /**
     * @ORM\Column(type="string", length=25)
     * (label="Código do Exemplar", showOnBrowse="true", showOnCreate="true", showOnUpdate="true", formViewOrder="1", required="true")
     */
    private $codigo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    public function __construct()
    {
        $this->emprestimoBibliotecas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivroBiblioteca(): ?LivroBiblioteca
    {
        return $this->livro_biblioteca;
    }

    public function setLivroBiblioteca(?LivroBiblioteca $livro_biblioteca): self
    {
        $this->livro_biblioteca = $livro_biblioteca;

        return $this;
    }

    /**
     * @return Collection|EmprestimoBiblioteca[]
     */
    public function getEmprestimoBibliotecas(): Collection
    {
        return $this->emprestimoBibliotecas;
    }

    public function addEmprestimoBiblioteca(EmprestimoBiblioteca $emprestimoBiblioteca): self
    {
        if ($this->emprestimoBibliotecas->contains($emprestimoBiblioteca) === false) {
            $this->emprestimoBibliotecas[] = $emprestimoBiblioteca;
            $emprestimoBiblioteca->addLivroBibliotecaExemplar($this);
        }

        return $this;
    }

    public function removeEmprestimoBiblioteca(EmprestimoBiblioteca $emprestimoBiblioteca): self
    {
        if ($this->emprestimoBibliotecas->contains($emprestimoBiblioteca) === true) {
            $this->emprestimoBibliotecas->removeElement($emprestimoBiblioteca);
            $emprestimoBiblioteca->removeLivroBibliotecaExemplar($this);
        }

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

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


}
