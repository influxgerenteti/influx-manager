<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FuncionarioValorHoraRepository")
 * @ORM\Table(name="funcionario_valor_hora",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="indice_relacional",
 *              columns={"valor_hora_id", "funcionario_id"}
 *          )
 *      }
 * )
 */
class FuncionarioValorHora
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ValorHora")
     * @ORM\JoinColumn(nullable=false)
     */
    private $valor_hora;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2)
     */
    private $valor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Funcionario", inversedBy="funcionarioValorHoras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $funcionario;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $valor_bonus = 0;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, options={"default": 0})
     */
    private $valor_extra = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValorHora(): ?ValorHora
    {
        return $this->valor_hora;
    }

    public function setValorHora(?ValorHora $valor_hora): self
    {
        $this->valor_hora = $valor_hora;

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

    public function getFuncionario(): ?Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(?Funcionario $funcionario): self
    {
        $this->funcionario = $funcionario;

        return $this;
    }

    public function getValorBonus()
    {
        return $this->valor_bonus;
    }

    public function setValorBonus($valor_bonus): self
    {
        $this->valor_bonus = $valor_bonus;

        return $this;
    }

    public function getValorExtra()
    {
        return $this->valor_extra;
    }

    public function setValorExtra($valor_extra): self
    {
        $this->valor_extra = $valor_extra;

        return $this;
    }


}
