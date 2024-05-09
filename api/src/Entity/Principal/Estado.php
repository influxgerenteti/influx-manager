<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\EstadoRepository")
 */
class Estado
{

    /**
     * Joins in the "update" view
     */
    public static $updateJoins = ['cidades'];

    function __construct()
    {
        $this->cidades = new ArrayCollection();
        $this->ceps    = new ArrayCollection();
    }

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=255)
     * (label="Nome", showOnBrowse="true", showOnUpdate="true", formViewClass="col-md-3")
     */
    private $nome;

    /**
     *
     * @ORM\Column(type="string", length=3)
     * (label="Sigla", showOnBrowse="true", showOnUpdate="true", formViewClass="col-md-3", formViewBreakAfter="true")
     */
    private $sigla;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Cidade", mappedBy="estado")
     * (label="Cidades", showOnBrowse="false", showOnUpdate="true", oneToManyTableColumns="nome", queryColumn="nome", descriptionColumn="nome", valueColumn="id")
     */
    private $cidades;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Cep", mappedBy="estado", orphanRemoval=true)
     */
    private $ceps;

    public function getId()
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getSigla(): ?string
    {
        return $this->sigla;
    }

    public function setSigla(string $sigla): self
    {
        $this->sigla = $sigla;

        return $this;
    }

    /**
     *
     * @return Collection|Cidade[]
     */
    public function getCidades(): Collection
    {
        return $this->cidades;
    }

    public function addCidade(Cidade $cidade): self
    {
        if ($this->cidades->contains($cidade) === false) {
            $this->cidades[] = $cidade;
            $cidade->setTituloPagar($this);
        }

        return $this;
    }

    public function removeCidade(Cidade $cidade): self
    {
        if ($this->cidades->contains($cidade) === true) {
            $this->cidades->removeElement($cidade);
        }

        return $this;
    }

    /**
     * @return Collection|Cep[]
     */
    public function getCeps(): Collection
    {
        return $this->ceps;
    }

    public function addCep(Cep $cep): self
    {
        if ($this->ceps->contains($cep) === false) {
            $this->ceps[] = $cep;
            $cep->setEstado($this);
        }

        return $this;
    }

    public function removeCep(Cep $cep): self
    {
        if ($this->ceps->contains($cep) === true) {
            $this->ceps->removeElement($cep);
            // set the owning side to null (unless already changed)
            if ($cep->getEstado() === $this) {
                $cep->setEstado(null);
            }
        }

        return $this;
    }


}
