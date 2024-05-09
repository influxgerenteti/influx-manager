<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ParametrosFranqueadoraRepository")
 * @ORM\Table(name="parametros_franqueadora")
 */
class ParametrosFranqueadora
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dias_variacao_vencimento;

    /**
     *
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $percentual_variacao_valores;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 6})
     */
    private $numero_maximo_parcelas = 6;

    /**
     * @ORM\Column(type="integer", options={"default":"7","comment":"Periodo em dias para reativacao automatica do Interessado"})
     */
    private $dias_reativacao_interessado = 7;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $nota_corte_media = 7.0;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $nota_conceitual_corte_media = 7.5;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiasVariacaoVencimento(): ?int
    {
        return $this->dias_variacao_vencimento;
    }

    public function setDiasVariacaoVencimento(?int $dias_variacao_vencimento): self
    {
        $this->dias_variacao_vencimento = $dias_variacao_vencimento;

        return $this;
    }

    public function getPercentualVariacaoValores()
    {
        return $this->percentual_variacao_valores;
    }

    public function setPercentualVariacaoValores($percentual_variacao_valores): self
    {
        $this->percentual_variacao_valores = $percentual_variacao_valores;

        return $this;
    }

    public function getNumeroMaximoParcelas(): ?int
    {
        return $this->numero_maximo_parcelas;
    }

    public function setNumeroMaximoParcelas(?int $numero_maximo_parcelas): self
    {
        $this->numero_maximo_parcelas = $numero_maximo_parcelas;

        return $this;
    }

    public function getDiasReativacaoInteressado(): ?int
    {
        return $this->dias_reativacao_interessado;
    }

    public function setDiasReativacaoInteressado(int $dias_reativacao_interessado): self
    {
        $this->dias_reativacao_interessado = $dias_reativacao_interessado;

        return $this;
    }

    public function getNotaCorteMedia()
    {
        return $this->nota_corte_media;
    }

    public function setNotaCorteMedia($nota_corte_media): self
    {
        $this->nota_corte_media = $nota_corte_media;

        return $this;
    }

    public function getNotaConceitualCorteMedia()
    {
        return $this->nota_conceitual_corte_media;
    }

    public function setNotaConceitualCorteMedia($nota_conceitual_corte_media): self
    {
        $this->nota_conceitual_corte_media = $nota_conceitual_corte_media;

        return $this;
    }


}
