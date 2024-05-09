<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\CidadeRepository")
 */
class Cidade
{
    /**
     * Joins in the "browse" view
     */
    public static $browseJoins = ['estado'];

    /**
     * Joins in the "update" view
     */
    public static $updateJoins = ['estado'];

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
     * (label="Nome", showOnBrowse="true", showOnUpdate="true")
     */
    private $nome;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Estado", inversedBy="cidades", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * (label="Estado", showOnBrowse="true", showOnUpdate="true", descriptionColumn="nome", valueColumn="id", findType="typeahead", queryColumn="nome,sigla", findQuery="teste=teste&")
     */
    private $estado;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Cep", mappedBy="cidade", orphanRemoval=true)
     */
    private $ceps;

    public function __construct()
    {
        $this->ceps = new ArrayCollection();
    }

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

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): self
    {
        $this->estado = $estado;

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
            $cep->setCidade($this);
        }

        return $this;
    }

    public function removeCep(Cep $cep): self
    {
        if ($this->ceps->contains($cep) === true) {
            $this->ceps->removeElement($cep);
            // set the owning side to null (unless already changed)
            if ($cep->getCidade() === $this) {
                $cep->setCidade(null);
            }
        }

        return $this;
    }


}
