<?php

namespace App\Entity\Importacao;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Funcionario
 *
 * @ORM\Table(name="funcionario",                                                 indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\FuncionarioRepository")
 */
class Funcionario
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="franqueada_id", type="integer", nullable=false)
     */
    private $franqueada_id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome_abreviado", type="string", length=50, nullable=true)
     */
    private $nome_abreviado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome", type="string", length=255, nullable=true)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estado_civil", type="string", length=100, nullable=true)
     */
    private $estado_civil;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cidade", type="string", length=255, nullable=true)
     */
    private $cidade;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bairro", type="string", length=255, nullable=true)
     */
    private $bairro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="endereco", type="string", length=255, nullable=true)
     */
    private $endereco;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cep", type="string", length=10, nullable=true)
     */
    private $cep;

    /**
     * @var string|null
     *
     * @ORM\Column(name="complemento", type="string", length=255, nullable=true)
     */
    private $complemento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="usuario", type="string", length=255, nullable=true)
     */
    private $usuario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sexo", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="M - Masculino, F - Feminino"})
     */
    private $sexo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data_nascimento", type="string", length=10, nullable=true)
     */
    private $data_nascimento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cpf", type="string", length=20, nullable=true)
     */
    private $cpf;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rg", type="string", length=20, nullable=true)
     */
    private $rg;

    /**
     * @var string|null
     *
     * @ORM\Column(name="carteira_profissional", type="string", length=20, nullable=true)
     */
    private $carteira_profissional;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telefone", type="string", length=20, nullable=true)
     */
    private $telefone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="celular", type="string", length=20, nullable=true)
     */
    private $celular;

    /**
     * @var string|null
     *
     * @ORM\Column(name="salario", type="string", length=20, nullable=true)
     */
    private $salario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="observacao", type="text", nullable=true)
     */
    private $observacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="professor", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="S - Sim, N - Não"})
     */
    private $professor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="situacao", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="A - Ativo, I - Inativo"})
     */
    private $situacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="valor_hora_aula", type="string", length=10, nullable=true)
     */
    private $valor_hora_aula;

    /**
     * @var string|null
     *
     * @ORM\Column(name="valor_hora_aula_externa", type="string", length=10, nullable=true)
     */
    private $valor_hora_aula_externa;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inss", type="string", length=20, nullable=true)
     */
    private $inss;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="conta", type="string", length=20, nullable=true)
     */
    private $conta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="agencia", type="string", length=10, nullable=true)
     */
    private $agencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome_banco", type="string", length=255, nullable=true)
     */
    private $nome_banco;

    /**
     * @var string|null
     *
     * @ORM\Column(name="atendente", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="S - Sim, N - Não"})
     */
    private $atendente;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data_admissao", type="string", length=10, nullable=true)
     */
    private $data_admissao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data_cadastro", type="string", length=10, nullable=true)
     */
    private $data_cadastro;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\Cheque", mappedBy="funcionario")
     */
    private $cheques;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\Contrato", mappedBy="funcionario")
     */
    private $contratos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\ContratoAulaLivre", mappedBy="funcionario")
     */
    private $contratoAulaLivres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\ContasPagar", mappedBy="funcionario")
     */
    private $contasPagars;

    public function __construct()
    {
        $this->contratos          = new ArrayCollection();
        $this->contratoAulaLivres = new ArrayCollection();
        $this->contasPagars       = new ArrayCollection();
        $this->cheques            = new ArrayCollection();
    }

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

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getNomeAbreviado(): ?string
    {
        return $this->nome_abreviado;
    }

    public function setNomeAbreviado(?string $nome_abreviado): self
    {
        $this->nome_abreviado = $nome_abreviado;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getEstadoCivil(): ?string
    {
        return $this->estado_civil;
    }

    public function setEstadoCivil(?string $estado_civil): self
    {
        $this->estado_civil = $estado_civil;

        return $this;
    }

    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    public function setCidade(?string $cidade): self
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    public function setBairro(?string $bairro): self
    {
        $this->bairro = $bairro;

        return $this;
    }

    public function getEndereco(): ?string
    {
        return $this->endereco;
    }

    public function setEndereco(?string $endereco): self
    {
        $this->endereco = $endereco;

        return $this;
    }

    public function getCep(): ?string
    {
        return $this->cep;
    }

    public function setCep(?string $cep): self
    {
        $this->cep = $cep;

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

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(?string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getDataNascimento(): ?string
    {
        return $this->data_nascimento;
    }

    public function setDataNascimento(?string $data_nascimento): self
    {
        $this->data_nascimento = $data_nascimento;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(?string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getRg(): ?string
    {
        return $this->rg;
    }

    public function setRg(?string $rg): self
    {
        $this->rg = $rg;

        return $this;
    }

    public function getCarteiraProfissional(): ?string
    {
        return $this->carteira_profissional;
    }

    public function setCarteiraProfissional(?string $carteira_profissional): self
    {
        $this->carteira_profissional = $carteira_profissional;

        return $this;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(?string $telefone): self
    {
        $this->telefone = $telefone;

        return $this;
    }

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular(?string $celular): self
    {
        $this->celular = $celular;

        return $this;
    }

    public function getSalario(): ?string
    {
        return $this->salario;
    }

    public function setSalario(?string $salario): self
    {
        $this->salario = $salario;

        return $this;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    public function setObservacao($observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getProfessor(): ?string
    {
        return $this->professor;
    }

    public function setProfessor(?string $professor): self
    {
        $this->professor = $professor;

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

    public function getValorHoraAula(): ?string
    {
        return $this->valor_hora_aula;
    }

    public function setValorHoraAula(?string $valor_hora_aula): self
    {
        $this->valor_hora_aula = $valor_hora_aula;

        return $this;
    }

    public function getValorHoraAulaExterna(): ?string
    {
        return $this->valor_hora_aula_externa;
    }

    public function setValorHoraAulaExterna(?string $valor_hora_aula_externa): self
    {
        $this->valor_hora_aula_externa = $valor_hora_aula_externa;

        return $this;
    }

    public function getInss(): ?string
    {
        return $this->inss;
    }

    public function setInss(?string $inss): self
    {
        $this->inss = $inss;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function setAgencia(?string $agencia): self
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function getNomeBanco(): ?string
    {
        return $this->nome_banco;
    }

    public function setNomeBanco(?string $nome_banco): self
    {
        $this->nome_banco = $nome_banco;

        return $this;
    }

    public function getAtendente(): ?string
    {
        return $this->atendente;
    }

    public function setAtendente(?string $atendente): self
    {
        $this->atendente = $atendente;

        return $this;
    }

    public function getDataAdmissao(): ?string
    {
        return $this->data_admissao;
    }

    public function setDataAdmissao(?string $data_admissao): self
    {
        $this->data_admissao = $data_admissao;

        return $this;
    }

    public function getDataCadastro(): ?string
    {
        return $this->data_cadastro;
    }

    public function setDataCadastro(?string $data_cadastro): self
    {
        $this->data_cadastro = $data_cadastro;

        return $this;
    }

    /**
     * @return Collection|Cheque[]
     */
    public function getCheques(): Collection
    {
        return $this->cheques;
    }

    public function addCheque(Cheque $cheque): self
    {
        if ($this->cheques->contains($cheque) === false) {
            $this->cheques[] = $cheque;
            $cheque->setFuncionario($this);
        }

        return $this;
    }

    public function removeCheque(Cheque $cheque): self
    {
        if ($this->cheques->contains($cheque) === true) {
            $this->cheques->removeElement($cheque);
            // set the owning side to null (unless already changed)
            if ($cheque->getFuncionario() === $this) {
                $cheque->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contrato[]
     */
    public function getContratos(): Collection
    {
        return $this->contratos;
    }

    public function addContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === false) {
            $this->contratos[] = $contrato;
            $contrato->setFuncionario($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getFuncionario() === $this) {
                $contrato->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ContratoAulaLivre[]
     */
    public function getContratoAulaLivres(): Collection
    {
        return $this->contratoAulaLivres;
    }

    public function addContratoAulaLivre(ContratoAulaLivre $contratoAulaLivre): self
    {
        if ($this->contratoAulaLivres->contains($contratoAulaLivre) === false) {
            $this->contratoAulaLivres[] = $contratoAulaLivre;
            $contratoAulaLivre->setFuncionario($this);
        }

        return $this;
    }

    public function removeContratoAulaLivre(ContratoAulaLivre $contratoAulaLivre): self
    {
        if ($this->contratoAulaLivres->contains($contratoAulaLivre) === true) {
            $this->contratoAulaLivres->removeElement($contratoAulaLivre);
            // set the owning side to null (unless already changed)
            if ($contratoAulaLivre->getFuncionario() === $this) {
                $contratoAulaLivre->setFuncionario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ContasPagar[]
     */
    public function getContasPagars(): Collection
    {
        return $this->contasPagars;
    }

    public function addContasPagar(ContasPagar $contasPagar): self
    {
        if ($this->contasPagars->contains($contasPagar) === false) {
            $this->contasPagars[] = $contasPagar;
            $contasPagar->setFuncionario($this);
        }

        return $this;
    }

    public function removeContasPagar(ContasPagar $contasPagar): self
    {
        if ($this->contasPagars->contains($contasPagar) === true) {
            $this->contasPagars->removeElement($contasPagar);
            // set the owning side to null (unless already changed)
            if ($contasPagar->getFuncionario() === $this) {
                $contasPagar->setFuncionario(null);
            }
        }

        return $this;
    }


}
