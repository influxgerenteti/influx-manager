<?php

namespace App\Entity\Importacao;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\ChequeRepository")
 */
class Cheque
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Aluno", inversedBy="cheques")
     * @ORM\JoinColumn(name="aluno_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Empresa", inversedBy="cheques")
     * @ORM\JoinColumn(name="empresa_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $empresa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Importacao\Funcionario", inversedBy="cheques")
     * @ORM\JoinColumn(name="funcionario_id",                           referencedColumnName="id", onDelete="SET NULL")
     */
    private $funcionario;

    /**
     * @ORM\Column(type="integer")
     */
    private $franqueada_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $aluno_nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $empresa_nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $funcionario_nome;

    /**
     * @ORM\Column(type="string", length=21, nullable=true)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $conta;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titular;

    /**
     * @ORM\Column(type="string", length=21, nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=21, nullable=true)
     */
    private $codigo_banco;

    /**
     * @ORM\Column(type="string", length=21, nullable=true)
     */
    private $agencia;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_compensacao;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_entrada;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_baixa;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $data_devolucao;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $valor;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"D - Devolvido, P - Pendente, Q - Quitado"})
     */
    private $situacao;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"comment":"P - Pagar, R - Receber"})
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $complemento;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motivo_devolucao;

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

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): self
    {
        $this->empresa = $empresa;

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

    public function getFranqueadaId(): ?int
    {
        return $this->franqueada_id;
    }

    public function setFranqueadaId(int $franqueada_id): self
    {
        $this->franqueada_id = $franqueada_id;

        return $this;
    }

    public function getAlunoNome(): ?string
    {
        return $this->aluno_nome;
    }

    public function setAlunoNome(?string $aluno_nome): self
    {
        $this->aluno_nome = $aluno_nome;

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

    public function getFuncionarioNome(): ?string
    {
        return $this->funcionario_nome;
    }

    public function setFuncionarioNome(?string $funcionario_nome): self
    {
        $this->funcionario_nome = $funcionario_nome;

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

    public function getConta(): ?string
    {
        return $this->conta;
    }

    public function setConta(?string $conta): self
    {
        $this->conta = $conta;

        return $this;
    }

    public function getTitular(): ?string
    {
        return $this->titular;
    }

    public function setTitular(?string $titular): self
    {
        $this->titular = $titular;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCodigoBanco(): ?string
    {
        return $this->codigo_banco;
    }

    public function setCodigoBanco(?string $codigo_banco): self
    {
        $this->codigo_banco = $codigo_banco;

        return $this;
    }

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function setAgencia(?string $agencia): self
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function getDataCompensacao(): ?string
    {
        return $this->data_compensacao;
    }

    public function setDataCompensacao(?string $data_compensacao): self
    {
        $this->data_compensacao = $data_compensacao;

        return $this;
    }

    public function getDataEntrada(): ?string
    {
        return $this->data_entrada;
    }

    public function setDataEntrada(?string $data_entrada): self
    {
        $this->data_entrada = $data_entrada;

        return $this;
    }

    public function getDataBaixa(): ?string
    {
        return $this->data_baixa;
    }

    public function setDataBaixa(?string $data_baixa): self
    {
        $this->data_baixa = $data_baixa;

        return $this;
    }

    public function getDataDevolucao(): ?string
    {
        return $this->data_devolucao;
    }

    public function setDataDevolucao(?string $data_devolucao): self
    {
        $this->data_devolucao = $data_devolucao;

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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(?string $situacao): self
    {
        $this->situacao = $situacao;

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

    public function getMotivoDevolucao(): ?string
    {
        return $this->motivo_devolucao;
    }

    public function setMotivoDevolucao(?string $motivo_devolucao): self
    {
        $this->motivo_devolucao = $motivo_devolucao;

        return $this;
    }


}
