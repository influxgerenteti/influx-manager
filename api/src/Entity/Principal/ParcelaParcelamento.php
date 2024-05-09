<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ParcelaParcelamentoRepository")
 * @ORM\Table(name="parcela_parcelamento")
 */
class ParcelaParcelamento
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ParcelamentoOperadoraCartao", inversedBy="parcelaParcelamentos")
     * @ORM\JoinColumn(nullable=false,                                                 onDelete="CASCADE")
     */
    private $parcelamento_operadora_cartao;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero_parcela;

    /**
     * @ORM\Column(type="integer")
     */
    private $dias_repasse;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $taxa;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParcelamentoOperadoraCartao(): ?ParcelamentoOperadoraCartao
    {
        return $this->parcelamento_operadora_cartao;
    }

    public function setParcelamentoOperadoraCartao(?ParcelamentoOperadoraCartao $parcelamento_operadora_cartao): self
    {
        $this->parcelamento_operadora_cartao = $parcelamento_operadora_cartao;

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

    public function getDiasRepasse(): ?int
    {
        return $this->dias_repasse;
    }

    public function setDiasRepasse(int $dias_repasse): self
    {
        $this->dias_repasse = $dias_repasse;

        return $this;
    }

    public function getTaxa()
    {
        return $this->taxa;
    }

    public function setTaxa($taxa): self
    {
        $this->taxa = $taxa;

        return $this;
    }


}
