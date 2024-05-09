<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TituloPagar
 *
 * @ORM\Table(name="titulo_pagar")
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TituloPagarRepository")
 */
class TituloPagar
{


    public function __construct()
    {
        $this->movimento_conta    = new ArrayCollection();
        $this->data_movimento     = new \DateTime();
        $this->data_vencimento    = new \DateTime();
        $this->data_prorrogacao   = new \DateTime();
        $this->tituloPagarCheques = new ArrayCollection();
    }

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_vencimento;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_prorrogacao;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_movimento;

    /**
     *
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $numero_parcela_documento;

    /**
     *
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_documento;

    /**
     *
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_juros_dia;

    /**
     *
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $percentual_multa;

    /**
     *
     * @var float|null
     *
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor_saldo;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="observacao", type="text", nullable=true)
     */
    private $observacao;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="situacao", type="string", length=3, nullable=false, options={"default"="PEN","fixed"=true,"comment"="PEN - Pendente, LIQ - Liquidado, BAI - Baixado, SUB - Substituido, DEV - Cheque Devolvido"})
     */
    private $situacao = 'PEN';

    /**
     *
     * @var \App\Entity\Principal\Conta
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Conta")
     * @ORM\JoinColumn(nullable=false)
     */

    private $conta;

    /**
     *
     * @var \App\Entity\Principal\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Pessoa")
     * @ORM\JoinColumn(nullable=false)
     */
    private $favorecido_pessoa;

    /**
     *
     * @var \App\Entity\Principal\Franqueada
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     *
     * @var \App\Entity\Principal\ContaPagar
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ContaPagar")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conta_pagar;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\MovimentoConta", mappedBy="titulo_pagar")
     */
    private $movimento_conta;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormaPagamento")
     * @ORM\JoinColumn(nullable=false)
     */
    private $forma_cobranca;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Cheque", inversedBy="tituloPagars")
     * @ORM\JoinColumn(name="cheque_id",                          referencedColumnName="id", onDelete="SET NULL")
     */
    private $cheque;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Cheque", mappedBy="titulo_pagar", cascade={"remove"})
     */
    private $tituloPagarCheques;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $narrativa_plano_conta;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $excluido = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataVencimento(): ?\DateTimeInterface
    {
        return $this->data_vencimento;
    }

    public function setDataVencimento(\DateTimeInterface $data_vencimento): self
    {
        $this->data_vencimento = $data_vencimento;

        return $this;
    }

    public function getDataProrrogacao(): ?\DateTimeInterface
    {
        return $this->data_prorrogacao;
    }

    public function setDataProrrogacao(\DateTimeInterface $data_prorrogacao): self
    {
        $this->data_prorrogacao = $data_prorrogacao;

        return $this;
    }

    public function getDataMovimento(): ?\DateTimeInterface
    {
        return $this->data_movimento;
    }

    public function setDataMovimento(\DateTimeInterface $data_movimento): self
    {
        $this->data_movimento = $data_movimento;

        return $this;
    }

    public function getNumeroParcelaDocumento(): ?int
    {
        return $this->numero_parcela_documento;
    }

    public function setNumeroParcelaDocumento(int $numero_parcela_documento): self
    {
        $this->numero_parcela_documento = $numero_parcela_documento;

        return $this;
    }

    public function getValorDocumento()
    {
        return $this->valor_documento;
    }

    public function setValorDocumento($valor_documento): self
    {
        $this->valor_documento = $valor_documento;

        return $this;
    }

    public function getValorJurosDia()
    {
        return $this->valor_juros_dia;
    }

    public function setValorJurosDia($valor_juros_dia): self
    {
        $this->valor_juros_dia = $valor_juros_dia;

        return $this;
    }

    public function getPercentualMulta()
    {
        return $this->percentual_multa;
    }

    public function setPercentualMulta($percentual_multa): self
    {
        $this->percentual_multa = $percentual_multa;

        return $this;
    }

    public function getValorSaldo()
    {
        return $this->valor_saldo;
    }

    public function setValorSaldo($valor_saldo): self
    {
        $this->valor_saldo = $valor_saldo;

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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getConta(): ?Conta
    {
        return $this->conta;
    }

    public function setConta(?Conta $conta): self
    {
        $this->conta = $conta;

        return $this;
    }

    public function getFavorecidoPessoa(): ?Pessoa
    {
        return $this->favorecido_pessoa;
    }

    public function setFavorecidoPessoa(?Pessoa $favorecido_pessoa): self
    {
        $this->favorecido_pessoa = $favorecido_pessoa;

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

    public function getContaPagar(): ?ContaPagar
    {
        return $this->conta_pagar;
    }

    public function setContaPagar(?ContaPagar $conta_pagar): self
    {
        $this->conta_pagar = $conta_pagar;

        return $this;
    }

    /**
     *
     * @return Collection|MovimentoConta[]
     */
    public function getMovimentoConta() : Collection
    {
        return $this->movimento_conta;
    }

    public function addMovimentoConta(MovimentoConta $movimentoConta) : self
    {
        if ($this->movimento_conta->contains($movimentoConta) === false) {
            $this->movimento_conta[] = $movimentoConta;
            $movimentoConta->setContaPagar($this);
        }

        return $this;
    }

    public function removeMovimentoConta(MovimentoConta $movimentoConta) : self
    {
        if ($this->movimento_conta->contains($movimentoConta) === true) {
            $this->movimento_conta->removeElement($movimentoConta);
            // set the owning side to null (unless already changed)
            if ($movimentoConta->getContaPagar() === $this) {
                $movimentoConta->setContaPagar(null);
            }
        }

        return $this;
    }

    public function getFormaCobranca() : ? FormaPagamento
    {
        return $this->forma_cobranca;
    }

    public function setFormaCobranca(? FormaPagamento $forma_cobranca) : self
    {
        $this->forma_cobranca = $forma_cobranca;

        return $this;
    }

    public function getCheque(): ?Cheque
    {
        return $this->cheque;
    }

    public function setCheque(?Cheque $cheque): self
    {
        $this->cheque = $cheque;

        return $this;
    }

    /**
     * @return Collection|Cheque[]
     */
    public function getTituloPagarCheques(): Collection
    {
        return $this->tituloPagarCheques;
    }

    public function addTituloPagarCheque(Cheque $tituloPagarCheque): self
    {
        if ($this->tituloPagarCheques->contains($tituloPagarCheque) === false) {
            $this->tituloPagarCheques[] = $tituloPagarCheque;
            $tituloPagarCheque->setTituloPagar($this);
        }

        return $this;
    }

    public function removeTituloPagarCheque(Cheque $tituloPagarCheque): self
    {
        if ($this->tituloPagarCheques->contains($tituloPagarCheque) === true) {
            $this->tituloPagarCheques->removeElement($tituloPagarCheque);
            // set the owning side to null (unless already changed)
            if ($tituloPagarCheque->getTituloPagar() === $this) {
                $tituloPagarCheque->setTituloPagar(null);
            }
        }

        return $this;
    }

    public function getNarrativaPlanoConta(): ?string
    {
        return $this->narrativa_plano_conta;
    }

    public function setNarrativaPlanoConta(?string $narrativa_plano_conta): self
    {
        $this->narrativa_plano_conta = $narrativa_plano_conta;

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


}
