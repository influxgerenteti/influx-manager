<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MotivoDevolucaoChequeRepository")
 * @ORM\Table(name="motivo_devolucao_cheque")
 */
class MotivoDevolucaoCheque
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private $descricao;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"A - ATIVO, I - INATIVO"})
     */
    private $situacao = 'A';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Cheque", mappedBy="motivo_devolucao_cheque")
     */
    private $cheques;

    public function __construct()
    {
        $this->cheques = new ArrayCollection();
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

    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    public function setCodigo(int $codigo): self
    {
        $this->codigo = $codigo;

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
     * @return Collection|Cheque[]
     */
    public function getCheques(): Collection
    {
        return $this->cheques;
    }

    public function addCheque(Cheque $cheque): self
    {
        if ($this->cheques->contains($cheque) === false) {
            $this->cheques[] = $cheque;
            $cheque->setMotivoDevolucaoCheque($this);
        }

        return $this;
    }

    public function removeCheque(Cheque $cheque): self
    {
        if ($this->cheques->contains($cheque) === true) {
            $this->cheques->removeElement($cheque);
            // set the owning side to null (unless already changed)
            if ($cheque->getMotivoDevolucaoCheque() === $this) {
                $cheque->setMotivoDevolucaoCheque(null);
            }
        }

        return $this;
    }


}
