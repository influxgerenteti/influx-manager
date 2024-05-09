<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ReciboRepository")
 */
class Recibo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero_recibo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_geracao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="recibosGerados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="recibosFranqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash_nome_arquivo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MovimentoConta", mappedBy="recibo")
     */
    private $movimentoContas;

    public function __construct()
    {
        $this->movimentoContas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroRecibo(): ?int
    {
        return $this->numero_recibo;
    }

    public function setNumeroRecibo(int $numero_recibo): self
    {
        $this->numero_recibo = $numero_recibo;

        return $this;
    }

    public function getDataGeracao(): ?\DateTimeInterface
    {
        return $this->data_geracao;
    }

    public function setDataGeracao(\DateTimeInterface $data_geracao): self
    {
        $this->data_geracao = $data_geracao;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

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

    public function getHashNomeArquivo(): ?string
    {
        return $this->hash_nome_arquivo;
    }

    public function setHashNomeArquivo(string $hash_nome_arquivo): self
    {
        $this->hash_nome_arquivo = $hash_nome_arquivo;

        return $this;
    }

    /**
     * @return Collection|MovimentoConta[]
     */
    public function getMovimentoContas(): Collection
    {
        return $this->movimentoContas;
    }

    public function addMovimentoConta(MovimentoConta $movimentoConta): self
    {
        if ($this->movimentoContas->contains($movimentoConta) === false) {
            $this->movimentoContas[] = $movimentoConta;
            $movimentoConta->setRecibo($this);
        }

        return $this;
    }

    public function removeMovimentoConta(MovimentoConta $movimentoConta): self
    {
        if ($this->movimentoContas->contains($movimentoConta) === true) {
            $this->movimentoContas->removeElement($movimentoConta);
            // set the owning side to null (unless already changed)
            if ($movimentoConta->getRecibo() === $this) {
                $movimentoConta->setRecibo(null);
            }
        }

        return $this;
    }


}
