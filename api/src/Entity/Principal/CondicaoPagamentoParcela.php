<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\CondicaoPagamentoParcelaRepository")
 * @ORM\Table(name="condicao_pagamento_parcela")
 */
class CondicaoPagamentoParcela
{
    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\CondicaoPagamento", inversedBy="condicaoPagamentoParcelas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $condicao_pagamento;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $numero_parcela;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $dias_vencimento;

    /**
     *
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $percentual_parcela;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCondicaoPagamento(): ?CondicaoPagamento
    {
        return $this->condicao_pagamento;
    }

    public function setCondicaoPagamento(?CondicaoPagamento $condicao_pagamento): self
    {
        $this->condicao_pagamento = $condicao_pagamento;

        return $this;
    }

    public function getNumeroParcela(): ?int
    {
        return $this->numero_parcela;
    }

    public function setNumeroParcela(int $numero_parcela): self
    {
        $this->numero_parcela = $numero_parcela;

        return $this;
    }

    public function getDiasVencimento(): ?int
    {
        return $this->dias_vencimento;
    }

    public function setDiasVencimento(int $dias_vencimento): self
    {
        $this->dias_vencimento = $dias_vencimento;

        return $this;
    }

    public function getPercentualParcela()
    {
        return $this->percentual_parcela;
    }

    public function setPercentualParcela($percentual_parcela): self
    {
        $this->percentual_parcela = $percentual_parcela;

        return $this;
    }


}
