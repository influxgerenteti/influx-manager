<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TipoItemRepository")
 */
class TipoItem
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
     * @ORM\Column(type="string", length=4)
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoOcorrencia", inversedBy="tipoItems")
     */
    private $tipo_ocorrencia;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Item", mappedBy="tipo_item")
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getTipoOcorrencia(): ?TipoOcorrencia
    {
        return $this->tipo_ocorrencia;
    }

    public function setTipoOcorrencia(?TipoOcorrencia $tipo_ocorrencia): self
    {
        $this->tipo_ocorrencia = $tipo_ocorrencia;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if ($this->items->contains($item) === false) {
            $this->items[] = $item;
            $item->setTipoItem($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->contains($item) === true) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getTipoItem() === $this) {
                $item->setTipoItem(null);
            }
        }

        return $this;
    }


}
