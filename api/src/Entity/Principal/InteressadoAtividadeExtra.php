<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\InteressadoAtividadeExtraRepository")
 * @ORM\Table(name="interessado_atividade_extra")
 */
class InteressadoAtividadeExtra
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Interessado", inversedBy="interessadoAtividadeExtras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $interessado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Livro", inversedBy="interessadoAtividadeExtras")
     * @ORM\JoinColumn(nullable=true)
     */
    private $livro;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\AtividadeExtra", inversedBy="interessadoAtividadeExtra", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $atividade_extra;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInteressado(): ?Interessado
    {
        return $this->interessado;
    }

    public function setInteressado(?Interessado $interessado): self
    {
        $this->interessado = $interessado;

        return $this;
    }

    public function getLivro(): ?Livro
    {
        return $this->livro;
    }

    public function setLivro(?Livro $livro): self
    {
        $this->livro = $livro;

        return $this;
    }

    public function getAtividadeExtra(): ?AtividadeExtra
    {
        return $this->atividade_extra;
    }

    public function setAtividadeExtra(AtividadeExtra $atividade_extra): self
    {
        $this->atividade_extra = $atividade_extra;

        return $this;
    }


}
