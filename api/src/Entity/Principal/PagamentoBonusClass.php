<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\PagamentoBonusClassRepository")
 */
class PagamentoBonusClass
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\CabecalhoPagamento", inversedBy="pagamentoBonusClasses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cabecalho_pagamento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\BonusClass", inversedBy="pagamentoBonusClasses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bonus_class;

    /**
     * @ORM\Column(type="string", length=3, options={"default": "PEN", "comment": "(PEN)dente, (P)agamento (E)m (A)ndamento, (PAG)a, (CAN)celada"})
     */
    private $situacao = 'PEN';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCabecalhoPagamento(): ?CabecalhoPagamento
    {
        return $this->cabecalho_pagamento;
    }

    public function setCabecalhoPagamento(?CabecalhoPagamento $cabecalho_pagamento): self
    {
        $this->cabecalho_pagamento = $cabecalho_pagamento;

        return $this;
    }

    public function getBonusClass(): ?BonusClass
    {
        return $this->bonus_class;
    }

    public function setBonusClass(?BonusClass $bonus_class): self
    {
        $this->bonus_class = $bonus_class;

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


}
