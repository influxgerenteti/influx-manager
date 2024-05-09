<?php

namespace App\Entity\Importacao;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Empresa
 *
 * @ORM\Table(name="empresa",                                                 indexes={@ORM\Index(name="IDX_FRANQUEADA", columns={"franqueada_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\EmpresaRepository")
 */
class Empresa
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
     * @ORM\Column(name="nome", type="string", length=255, nullable=true)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="razao_social", type="string", length=255, nullable=true)
     */
    private $razao_social;

    /**
     * @var string|null
     *
     * @ORM\Column(name="endereco", type="string", length=255, nullable=true)
     */
    private $endereco;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bairro", type="string", length=255, nullable=true)
     */
    private $bairro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cidade", type="string", length=255, nullable=true)
     */
    private $cidade;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cep", type="string", length=10, nullable=true)
     */
    private $cep;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cnpj", type="string", length=20, nullable=true)
     */
    private $cnpj;

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
     * @ORM\Column(name="complemento", type="string", length=100, nullable=true)
     */
    private $complemento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inscricao", type="string", length=100, nullable=true)
     */
    private $inscricao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data_cadastro", type="string", length=10, nullable=true)
     */
    private $data_cadastro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="situacao", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="A - Ativo, I - Inativo"})
     */
    private $situacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telefone", type="string", length=20, nullable=true)
     */
    private $telefone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ramal", type="string", length=10, nullable=true)
     */
    private $ramal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fax", type="string", length=20, nullable=true)
     */
    private $fax;

    /**
     * @var string|null
     *
     * @ORM\Column(name="celular", type="string", length=20, nullable=true)
     */
    private $celular;

    /**
     * @var string|null
     *
     * @ORM\Column(name="agencia", type="string", length=20, nullable=true)
     */
    private $agencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="conta", type="string", length=20, nullable=true)
     */
    private $conta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo_cliente_banco", type="string", length=20, nullable=true)
     */
    private $codigo_cliente_banco;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cliente", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="S - Sim, N - Não"})
     */
    private $cliente;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fornecedor", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="S - Sim, N - Não"})
     */
    private $fornecedor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="aluno_escola", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="S - Sim, N - Não"})
     */
    private $aluno_escola;

    /**
     * @var string|null
     *
     * @ORM\Column(name="aluno_empresa", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="S - Sim, N - Não"})
     */
    private $aluno_empresa;

    /**
     * @var string|null
     *
     * @ORM\Column(name="observacao", type="text", nullable=true)
     */
    private $observacao;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\ContasPagar", mappedBy="empresa")
     */
    private $contasPagars;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\Caixa", mappedBy="empresa")
     */
    private $caixas;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\Cheque", mappedBy="empresa")
     */
    private $cheques;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\ContasReceber", mappedBy="empresa")
     */
    private $contasRecebers;

    public function __construct()
    {
        $this->contasPagars   = new ArrayCollection();
        $this->caixas         = new ArrayCollection();
        $this->cheques        = new ArrayCollection();
        $this->contasRecebers = new ArrayCollection();
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

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getRazaoSocial(): ?string
    {
        return $this->razao_social;
    }

    public function setRazaoSocial(?string $razao_social): self
    {
        $this->razao_social = $razao_social;

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

    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    public function setBairro(?string $bairro): self
    {
        $this->bairro = $bairro;

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

    public function getCep(): ?string
    {
        return $this->cep;
    }

    public function setCep(?string $cep): self
    {
        $this->cep = $cep;

        return $this;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function setCnpj(?string $cnpj): self
    {
        $this->cnpj = $cnpj;

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

    public function getComplemento(): ?string
    {
        return $this->complemento;
    }

    public function setComplemento(?string $complemento): self
    {
        $this->complemento = $complemento;

        return $this;
    }

    public function getInscricao(): ?string
    {
        return $this->inscricao;
    }

    public function setInscricao(?string $inscricao): self
    {
        $this->inscricao = $inscricao;

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

    public function getDataCadastro(): ?string
    {
        return $this->data_cadastro;
    }

    public function setDataCadastro(?string $data_cadastro): self
    {
        $this->data_cadastro = $data_cadastro;

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

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(?string $telefone): self
    {
        $this->telefone = $telefone;

        return $this;
    }

    public function getRamal(): ?string
    {
        return $this->ramal;
    }

    public function setRamal(?string $ramal): self
    {
        $this->ramal = $ramal;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

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

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function setAgencia(?string $agencia): self
    {
        $this->agencia = $agencia;

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

    public function getCodigoClienteBanco(): ?string
    {
        return $this->codigo_cliente_banco;
    }

    public function setCodigoClienteBanco(?string $codigo_cliente_banco): self
    {
        $this->codigo_cliente_banco = $codigo_cliente_banco;

        return $this;
    }

    public function getCliente(): ?string
    {
        return $this->cliente;
    }

    public function setCliente(?string $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function getFornecedor(): ?string
    {
        return $this->fornecedor;
    }

    public function setFornecedor(?string $fornecedor): self
    {
        $this->fornecedor = $fornecedor;

        return $this;
    }

    public function getAlunoEscola(): ?string
    {
        return $this->aluno_escola;
    }

    public function setAlunoEscola(?string $aluno_escola): self
    {
        $this->aluno_escola = $aluno_escola;

        return $this;
    }

    public function getAlunoEmpresa(): ?string
    {
        return $this->aluno_empresa;
    }

    public function setAlunoEmpresa(?string $aluno_empresa): self
    {
        $this->aluno_empresa = $aluno_empresa;

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

    /**
     *
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
            $contasPagar->setEmpresa($this);
        }

        return $this;
    }

    public function removeContasPagar(ContasPagar $contasPagar): self
    {
        if ($this->contasPagars->contains($contasPagar) === true) {
            $this->contasPagars->removeElement($contasPagar);
            // set the owning side to null (unless already changed)
            if ($contasPagar->getEmpresa() === $this) {
                $contasPagar->setEmpresa(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|Caixa[]
     */
    public function getCaixas(): Collection
    {
        return $this->caixas;
    }

    public function addCaixa(Caixa $caixa): self
    {
        if ($this->caixas->contains($caixa) === false) {
            $this->caixas[] = $caixa;
            $caixa->setEmpresa($this);
        }

        return $this;
    }

    public function removeCaixa(Caixa $caixa): self
    {
        if ($this->caixas->contains($caixa) === true) {
            $this->caixas->removeElement($caixa);
            // set the owning side to null (unless already changed)
            if ($caixa->getEmpresa() === $this) {
                $caixa->setEmpresa(null);
            }
        }

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
            $cheque->setEmpresa($this);
        }

        return $this;
    }

    public function removeCheque(Cheque $cheque): self
    {
        if ($this->cheques->contains($cheque) === true) {
            $this->cheques->removeElement($cheque);
            // set the owning side to null (unless already changed)
            if ($cheque->getEmpresa() === $this) {
                $cheque->setEmpresa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ContasReceber[]
     */
    public function getContasRecebers(): Collection
    {
        return $this->contasRecebers;
    }

    public function addContasReceber(ContasReceber $contasReceber): self
    {
        if ($this->contasRecebers->contains($contasReceber) === false) {
            $this->contasRecebers[] = $contasReceber;
            $contasReceber->setEmpresa($this);
        }

        return $this;
    }

    public function removeContasReceber(ContasReceber $contasReceber): self
    {
        if ($this->contasRecebers->contains($contasReceber) === true) {
            $this->contasRecebers->removeElement($contasReceber);
            // set the owning side to null (unless already changed)
            if ($contasReceber->getEmpresa() === $this) {
                $contasReceber->setEmpresa(null);
            }
        }

        return $this;
    }


}
