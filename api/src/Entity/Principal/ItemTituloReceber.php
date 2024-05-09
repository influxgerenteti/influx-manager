<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\ORM\Mapping as ORM;

/**
 * !Atenção:
 * *classe ainda não está sendo utilizada. A tabela foi criada para manter uma relação entre o item e o titulo
 * *a receber, pois é uma relação N pra N que não estava relacionada anteriormente. Mas para implementar isto,
 * *seria necessário engessar um pouco o processo (atualmente o título está todo aberto ao usuário)
 * *A princípio seria feito nesta nova forma, mas no final decidiu-se não fazer por enquanto.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ItemTituloReceberRepository")
 * @ORM\Table(name="item_titulo_receber")
 * @author                                                                             Augusto Fleith Comitti <augusto.comitti@gatilabs.com.br>
 * @license                                                                            MIT https://gatilabs.com.br
 */
class ItemTituloReceber
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TituloReceber", inversedBy="itemsTituloReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $titulo_receber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Item", inversedBy="itemsItemContaReceber")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    private $valor;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTituloReceber(): ?TituloReceber
    {
        return $this->titulo_receber;
    }

    public function setTituloReceber(?TituloReceber $titulo_receber): self
    {
        $this->titulo_receber = $titulo_receber;

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


}
