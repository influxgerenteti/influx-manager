<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ChequeRepository")
 */
class Cheque
{


    function __construct()
    {
        $this->data_entrada   = new \DateTime();
        $this->situacao       = 'P';
        $this->tituloPagars   = new ArrayCollection();
        $this->tituloRecebers = new ArrayCollection();
    }
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="cheques")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="chequesAtendenteUsuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $atendente_usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa", inversedBy="chequesPessoa")
     */
    private $pessoa;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $titular;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $banco;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $agencia;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $conta;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_entrada;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_bom_para;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_baixa;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_devolucao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_segunda_devolucao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\MotivoDevolucaoCheque", inversedBy="cheques")
     */
    private $motivo_devolucao_cheque;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_desconto;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"P","comment":"P - Pendente, B - Baixado, D - Devolvido, C - Cancelado"})
     */
    private $situacao;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $complemento;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $excluido = false;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"P - Pagar, R - Receber"})
     */
    private $tipo = 'R';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\TituloPagar", mappedBy="cheque")
     */
    private $tituloPagars;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TituloPagar", inversedBy="tituloPagarCheques")
     */
    private $titulo_pagar;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TituloReceber", inversedBy="tituloReceberCheques")
     */
    private $titulo_receber;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\MovimentoConta", inversedBy="cheque", cascade={"persist", "remove"})
     */
    private $movimento_conta;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAtendenteUsuario(): ?Usuario
    {
        return $this->atendente_usuario;
    }

    public function setAtendenteUsuario(?Usuario $atendente_usuario): self
    {
        $this->atendente_usuario = $atendente_usuario;

        return $this;
    }

    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(?Pessoa $pessoa): self
    {
        $this->pessoa = $pessoa;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getTitular(): ?string
    {
        return $this->titular;
    }

    public function setTitular(string $titular): self
    {
        $this->titular = $titular;

        return $this;
    }

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function setAgencia(string $agencia): self
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function getConta(): ?string
    {
        return $this->conta;
    }

    public function setConta(string $conta): self
    {
        $this->conta = $conta;

        return $this;
    }

    public function getDataEntrada(): ?\DateTimeInterface
    {
        return $this->data_entrada;
    }

    public function setDataEntrada(\DateTimeInterface $data_entrada): self
    {
        $this->data_entrada = $data_entrada;

        return $this;
    }

    public function getDataBomPara(): ?\DateTimeInterface
    {
        return $this->data_bom_para;
    }

    public function setDataBomPara(?\DateTimeInterface $data_bom_para): self
    {
        $this->data_bom_para = $data_bom_para;

        return $this;
    }

    public function getDataBaixa(): ?\DateTimeInterface
    {
        return $this->data_baixa;
    }

    public function setDataBaixa(?\DateTimeInterface $data_baixa): self
    {
        $this->data_baixa = $data_baixa;

        return $this;
    }

    public function getDataDevolucao(): ?\DateTimeInterface
    {
        return $this->data_devolucao;
    }

    public function setDataDevolucao(?\DateTimeInterface $data_devolucao): self
    {
        $this->data_devolucao = $data_devolucao;

        return $this;
    }

    public function getDataSegundaDevolucao(): ?\DateTimeInterface
    {
        return $this->data_segunda_devolucao;
    }

    public function setDataSegundaDevolucao(?\DateTimeInterface $data_segunda_devolucao): self
    {
        $this->data_segunda_devolucao = $data_segunda_devolucao;

        return $this;
    }

    public function getMotivoDevolucaoCheque(): ?MotivoDevolucaoCheque
    {
        return $this->motivo_devolucao_cheque;
    }

    public function setMotivoDevolucaoCheque(?MotivoDevolucaoCheque $motivo_devolucao_cheque): self
    {
        $this->motivo_devolucao_cheque = $motivo_devolucao_cheque;

        return $this;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getValorDesconto()
    {
        return $this->valor_desconto;
    }

    public function setValorDesconto($valor_desconto): self
    {
        $this->valor_desconto = $valor_desconto;

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

    public function getComplemento(): ?string
    {
        return $this->complemento;
    }

    public function setComplemento(?string $complemento): self
    {
        $this->complemento = $complemento;

        return $this;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getExcluido(): ?bool
    {
        return $this->excluido;
    }

    public function setExcluido(bool $excluido): self
    {
        $this->excluido = $excluido;

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

    /**
     * @return Collection|TituloPagar[]
     */
    public function getTituloPagars(): Collection
    {
        return $this->tituloPagars;
    }

    public function addTituloPagar(TituloPagar $tituloPagar): self
    {
        if ($this->tituloPagars->contains($tituloPagar) === false) {
            $this->tituloPagars[] = $tituloPagar;
            $tituloPagar->setCheque($this);
        }

        return $this;
    }

    public function removeTituloPagar(TituloPagar $tituloPagar): self
    {
        if ($this->tituloPagars->contains($tituloPagar) === true) {
            $this->tituloPagars->removeElement($tituloPagar);
            // set the owning side to null (unless already changed)
            if ($tituloPagar->getCheque() === $this) {
                $tituloPagar->setCheque(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TituloReceber[]
     */
    public function getTituloRecebers(): Collection
    {
        return $this->tituloRecebers;
    }

    public function addTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === false) {
            $this->tituloRecebers[] = $tituloReceber;
            $tituloReceber->setCheque($this);
        }

        return $this;
    }

    public function removeTituloReceber(TituloReceber $tituloReceber): self
    {
        if ($this->tituloRecebers->contains($tituloReceber) === true) {
            $this->tituloRecebers->removeElement($tituloReceber);
            // set the owning side to null (unless already changed)
            if ($tituloReceber->getCheque() === $this) {
                $tituloReceber->setCheque(null);
            }
        }

        return $this;
    }

    public function getTituloPagar(): ?TituloPagar
    {
        return $this->titulo_pagar;
    }

    public function setTituloPagar(?TituloPagar $titulo_pagar): self
    {
        $this->titulo_pagar = $titulo_pagar;

        return $this;
    }

    public function getTituloReceber(): ?TituloReceber
    {
        return $this->titulo_receber;
    }

    public function setTituloReceber(?TituloReceber $titulo_receber): self
    {
        $this->titulo_receber = $titulo_receber;

        return $this;
    }

    public function getBanco(): ?string
    {
        return $this->banco;
    }

    public function setBanco(string $banco): self
    {
        $this->banco = $banco;

        return $this;
    }

    public function getMovimentoConta(): ?MovimentoConta
    {
        return $this->movimento_conta;
    }

    public function setMovimentoConta(?MovimentoConta $movimento_conta): self
    {
        $this->movimento_conta = $movimento_conta;

        return $this;
    }


}
