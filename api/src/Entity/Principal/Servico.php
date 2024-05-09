<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ServicoRepository")
 */
class Servico
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="servicos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Item", inversedBy="servicos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="servicos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="servicos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_solicitacao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_conclusao;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantidade;

    /**
     * @ORM\Column(type="integer")
     */
    private $protocolo;

    /**
     * @ORM\Column(type="string", length=3, options={"comment":"EA - Em andamento, C - Concluido, CAN - Cancelado", "default":"EA"})
     */
    private $situacao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ServicoHistorico", mappedBy="servico")
     */
    private $servicoHistoricos;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ContaReceber", inversedBy="servicos")
     */
    private $conta_receber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormaPagamento", inversedBy="servicos")
     */
    private $forma_cobranca;

    function __construct()
    {
        $this->situacao          = "EA";
        $this->protocolo         = (new \DateTime())->getTimestamp();
        $this->servicoHistoricos = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAluno(): ?Aluno
    {
        return $this->aluno;
    }

    public function setAluno(?Aluno $aluno): self
    {
        $this->aluno = $aluno;

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

    public function getFuncionario(): ?Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(?Funcionario $funcionario): self
    {
        $this->funcionario = $funcionario;

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

    public function getDataSolicitacao(): ?\DateTimeInterface
    {
        return $this->data_solicitacao;
    }

    public function setDataSolicitacao(\DateTimeInterface $data_solicitacao): self
    {
        $this->data_solicitacao = $data_solicitacao;

        return $this;
    }

    public function getDataConclusao(): ?\DateTimeInterface
    {
        return $this->data_conclusao;
    }

    public function setDataConclusao(?\DateTimeInterface $data_conclusao): self
    {
        $this->data_conclusao = $data_conclusao;

        return $this;
    }

    public function getQuantidade(): ?int
    {
        return $this->quantidade;
    }

    public function setQuantidade(int $quantidade): self
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    public function getProtocolo(): ?int
    {
        return $this->protocolo;
    }

    public function setProtocolo(int $protocolo): self
    {
        $this->protocolo = $protocolo;

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
     * @return Collection|ServicoHistorico[]
     */
    public function getServicoHistoricos(): Collection
    {
        return $this->servicoHistoricos;
    }

    public function addServicoHistorico(ServicoHistorico $servicoHistorico): self
    {
        if ($this->servicoHistoricos->contains($servicoHistorico) === false) {
            $this->servicoHistoricos[] = $servicoHistorico;
            $servicoHistorico->setServico($this);
        }

        return $this;
    }

    public function removeServicoHistorico(ServicoHistorico $servicoHistorico): self
    {
        if ($this->servicoHistoricos->contains($servicoHistorico) === false) {
            $this->servicoHistoricos->removeElement($servicoHistorico);
            // set the owning side to null (unless already changed)
            if ($servicoHistorico->getServico() === $this) {
                $servicoHistorico->setServico(null);
            }
        }

        return $this;
    }

    public function getContaReceber(): ?ContaReceber
    {
        return $this->conta_receber;
    }

    public function setContaReceber(?ContaReceber $conta_receber): self
    {
        $this->conta_receber = $conta_receber;

        return $this;
    }

    public function getFormaCobranca(): ?FormaPagamento
    {
        return $this->forma_cobranca;
    }

    public function setFormaCobranca(?FormaPagamento $forma_cobranca): self
    {
        $this->forma_cobranca = $forma_cobranca;

        return $this;
    }


}
