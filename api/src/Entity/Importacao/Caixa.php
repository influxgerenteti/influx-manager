<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\CaixaRepository")
 */
class Caixa
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
    private $franqueada_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Empresa", inversedBy="caixas")
     * @ORM\JoinColumn(name="empresa_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $empresa;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $empresa_nome;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_lancamento;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=6, nullable=true)
     */
    private $valor;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $conta;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $tipo_recebimento;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $plano_conta;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $row_number;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $codigo_professor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $usuario;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $origem;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranqueadaId(): ?int
    {
        return $this->franqueada_id;
    }

    public function setFranqueadaId(int $franqueada_id): self
    {
        $this->franqueada_id = $franqueada_id;

        return $this;
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    public function getEmpresaNome(): ?string
    {
        return $this->empresa_nome;
    }

    public function setEmpresaNome(?string $empresa_nome): self
    {
        $this->empresa_nome = $empresa_nome;

        return $this;
    }

    public function getDataLancamento(): ?string
    {
        return $this->data_lancamento;
    }

    public function setDataLancamento(?string $data_lancamento): self
    {
        $this->data_lancamento = $data_lancamento;

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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getConta(): ?string
    {
        return $this->conta;
    }

    public function setConta(?string $conta): self
    {
        $this->conta = $conta;

        return $this;
    }

    public function getTipoRecebimento(): ?string
    {
        return $this->tipo_recebimento;
    }

    public function setTipoRecebimento(?string $tipo_recebimento): self
    {
        $this->tipo_recebimento = $tipo_recebimento;

        return $this;
    }

    public function getPlanoConta(): ?string
    {
        return $this->plano_conta;
    }

    public function setPlanoConta(?string $plano_conta): self
    {
        $this->plano_conta = $plano_conta;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getRowNumber(): ?int
    {
        return $this->row_number;
    }

    public function setRowNumber(?int $row_number): self
    {
        $this->row_number = $row_number;

        return $this;
    }

    public function getCodigoProfessor(): ?string
    {
        return $this->codigo_professor;
    }

    public function setCodigoProfessor(?string $codigo_professor): self
    {
        $this->codigo_professor = $codigo_professor;

        return $this;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getOrigem(): ?string
    {
        return $this->origem;
    }

    public function setOrigem(?string $origem): self
    {
        $this->origem = $origem;

        return $this;
    }


}
