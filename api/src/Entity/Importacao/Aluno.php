<?php

namespace App\Entity\Importacao;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Aluno
 *
 * @ORM\Table(name="aluno")
 * @ORM\Entity(repositoryClass="App\Repository\Importacao\AlunoRepository")
 */
class Aluno
{
    /**
     *
     * @var integer
     *
     * @ORM\Column(name="id",                   type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var integer|null
     *
     * @ORM\Column(name="franqueada_id", type="integer", nullable=false)
     */
    private $franqueada_id;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="aluno", type="string", length=255, nullable=true)
     */
    private $aluno;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="sexo", type="string", length=1, nullable=true, options={"fixed"=true,"comment"="M - Masculino, F - Feminino"})
     */
    private $sexo;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="numero_matricula", type="string", length=20, nullable=true)
     */
    private $numeroMatricula;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="data_nascimento", type="string", length=10, nullable=true)
     */
    private $dataNascimento;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="cidade", type="string", length=255, nullable=true)
     */
    private $cidade;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="bairro", type="string", length=255, nullable=true)
     */
    private $bairro;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="cpf", type="string", length=20, nullable=true)
     */
    private $cpf;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="rg", type="string", length=20, nullable=true)
     */
    private $rg;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="endereco", type="string", length=255, nullable=true)
     */
    private $endereco;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="cep", type="string", length=10, nullable=true)
     */
    private $cep;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="complemento", type="string", length=255, nullable=true)
     */
    private $complemento;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="telefone", type="string", length=20, nullable=true)
     */
    private $telefone;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="celular", type="string", length=20, nullable=true)
     */
    private $celular;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="telefone_comercial", type="string", length=20, nullable=true)
     */
    private $telefoneComercial;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="ramal", type="string", length=10, nullable=true)
     */
    private $ramal;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="profissao", type="string", length=255, nullable=true)
     */
    private $profissao;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="horario_contato", type="string", length=10, nullable=true)
     */
    private $horarioContato;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="data_cadastro", type="string", length=10, nullable=true)
     */
    private $dataCadastro;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="conta", type="string", length=20, nullable=true)
     */
    private $conta;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="agencia", type="string", length=20, nullable=true)
     */
    private $agencia;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="codigo_cli_banco", type="string", length=20, nullable=true)
     */
    private $codigoCliBanco;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="data_inclusao", type="string", length=10, nullable=true)
     */
    private $dataInclusao;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="situacao", type="string", length=2, nullable=true, options={"fixed"=true,"comment"="A - Ativo, I - Inativo, IN - Interessado, T - Trancado, CF - Captação Franqueadora"})
     */
    private $situacao;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="midia", type="string", length=255, nullable=true)
     */
    private $midia;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="tipo_contato", type="string", length=50, nullable=true)
     */
    private $tipoContato;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="atendente", type="string", length=255, nullable=true)
     */
    private $atendente;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="estado_civil", type="string", length=100, nullable=true)
     */
    private $estadoCivil;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\Responsavel", mappedBy="aluno")
     */
    private $responsavels;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\Cheque", mappedBy="aluno")
     */
    private $cheques;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\Contrato", mappedBy="aluno")
     */
    private $contratos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\ContratoAulaLivre", mappedBy="aluno")
     */
    private $contratoAulaLivres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Importacao\ContasReceber", mappedBy="aluno")
     */
    private $contasRecebers;

    public function __construct()
    {
        $this->responsavels       = new ArrayCollection();
        $this->cheques            = new ArrayCollection();
        $this->contratos          = new ArrayCollection();
        $this->contratoAulaLivres = new ArrayCollection();
        $this->contasRecebers     = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranqueadaId(): ?int
    {
        return $this->franqueada_id;
    }

    public function setFranqueadaId(?int $franqueada_id): self
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

    public function getAluno(): ?string
    {
        return $this->aluno;
    }

    public function setAluno(?string $aluno): self
    {
        $this->aluno = $aluno;

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

    public function getNumeroMatricula(): ?string
    {
        return $this->numeroMatricula;
    }

    public function setNumeroMatricula(?string $numeroMatricula): self
    {
        $this->numeroMatricula = $numeroMatricula;

        return $this;
    }

    public function getDataNascimento(): ?string
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento(?string $dataNascimento): self
    {
        $this->dataNascimento = $dataNascimento;

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

    public function getTelefoneComercial(): ?string
    {
        return $this->telefoneComercial;
    }

    public function setTelefoneComercial(?string $telefoneComercial): self
    {
        $this->telefoneComercial = $telefoneComercial;

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

    public function getProfissao(): ?string
    {
        return $this->profissao;
    }

    public function setProfissao(?string $profissao): self
    {
        $this->profissao = $profissao;

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

    public function getHorarioContato(): ?string
    {
        return $this->horarioContato;
    }

    public function setHorarioContato(?string $horarioContato): self
    {
        $this->horarioContato = $horarioContato;

        return $this;
    }

    public function getDataCadastro(): ?string
    {
        return $this->dataCadastro;
    }

    public function setDataCadastro(?string $dataCadastro): self
    {
        $this->dataCadastro = $dataCadastro;

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

    public function getCodigoCliBanco(): ?string
    {
        return $this->codigoCliBanco;
    }

    public function setCodigoCliBanco(?string $codigoCliBanco): self
    {
        $this->codigoCliBanco = $codigoCliBanco;

        return $this;
    }

    public function getDataInclusao(): ?string
    {
        return $this->dataInclusao;
    }

    public function setDataInclusao(?string $dataInclusao): self
    {
        $this->dataInclusao = $dataInclusao;

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

    public function getMidia(): ?string
    {
        return $this->midia;
    }

    public function setMidia(?string $midia): self
    {
        $this->midia = $midia;

        return $this;
    }

    public function getTipoContato(): ?string
    {
        return $this->tipoContato;
    }

    public function setTipoContato(?string $tipoContato): self
    {
        $this->tipoContato = $tipoContato;

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

    public function getEstadoCivil(): ?string
    {
        return $this->estadoCivil;
    }

    public function setEstadoCivil(?string $estadoCivil): self
    {
        $this->estadoCivil = $estadoCivil;

        return $this;
    }

    /**
     * @return Collection|Responsavel[]
     */
    public function getResponsavels(): Collection
    {
        return $this->responsavels;
    }

    public function addResponsavel(Responsavel $responsavel): self
    {
        if ($this->responsavels->contains($responsavel) === false) {
            $this->responsavels[] = $responsavel;
            $responsavel->setAluno($this);
        }

        return $this;
    }

    public function removeResponsavel(Responsavel $responsavel): self
    {
        if ($this->responsavels->contains($responsavel) === true) {
            $this->responsavels->removeElement($responsavel);
            // set the owning side to null (unless already changed)
            if ($responsavel->getAluno() === $this) {
                $responsavel->setAluno(null);
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
            $cheque->setAluno($this);
        }

        return $this;
    }

    public function removeCheque(Cheque $cheque): self
    {
        if ($this->cheques->contains($cheque) === true) {
            $this->cheques->removeElement($cheque);
            // set the owning side to null (unless already changed)
            if ($cheque->getAluno() === $this) {
                $cheque->setAluno(null);
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
            $contrato->setAluno($this);
        }

        return $this;
    }

    public function removeContrato(Contrato $contrato): self
    {
        if ($this->contratos->contains($contrato) === true) {
            $this->contratos->removeElement($contrato);
            // set the owning side to null (unless already changed)
            if ($contrato->getAluno() === $this) {
                $contrato->setAluno(null);
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
            $contratoAulaLivre->setAluno($this);
        }

        return $this;
    }

    public function removeContratoAulaLivre(ContratoAulaLivre $contratoAulaLivre): self
    {
        if ($this->contratoAulaLivres->contains($contratoAulaLivre) === true) {
            $this->contratoAulaLivres->removeElement($contratoAulaLivre);
            // set the owning side to null (unless already changed)
            if ($contratoAulaLivre->getAluno() === $this) {
                $contratoAulaLivre->setAluno(null);
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
            $contasReceber->setAluno($this);
        }

        return $this;
    }

    public function removeContasReceber(ContasReceber $contasReceber): self
    {
        if ($this->contasRecebers->contains($contasReceber) === true) {
            $this->contasRecebers->removeElement($contasReceber);
            // set the owning side to null (unless already changed)
            if ($contasReceber->getAluno() === $this) {
                $contasReceber->setAluno(null);
            }
        }

        return $this;
    }


}
