<?php
namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\UsuarioRepository")
 */
class Usuario
{


    public function __construct()
    {
        $this->data_criacao = new \DateTime();
        $this->franqueadas  = new ArrayCollection();
        $this->chequesAtendenteUsuario = new ArrayCollection();
        $this->usuarioContaReceber     = new ArrayCollection();
        $this->itemsContaReceber       = new ArrayCollection();
        $this->moduloUsuarioAcaos      = new ArrayCollection();
        $this->papels         = new ArrayCollection();
        $this->recibosGerados = new ArrayCollection();
        $this->formularioFollowUps = new ArrayCollection();
        $this->followupComercials  = new ArrayCollection();
        $this->notificacoes        = new ArrayCollection();
        $this->followupConvenios   = new ArrayCollection();
        $this->checklistAtividadeRealizadas = new ArrayCollection();
        $this->atividadeExtras         = new ArrayCollection();
        $this->historicoSituacaoAlunos = new ArrayCollection();
        $this->funcionarios            = new ArrayCollection();
        $this->ocorrenciaAcademicas    = new ArrayCollection();
        $this->reposicaoAulas          = new ArrayCollection();
        $this->token = new ArrayCollection();
        $this->agendaCompromissos    = new ArrayCollection();
        $this->historicoItems        = new ArrayCollection();
        $this->historicoEntregaItems = new ArrayCollection();
        $this->historicoEntregaItemsAutorizado = new ArrayCollection();
    }

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $senha;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_criacao;

    /**
     *
     * @ORM\Column(type="string", length=2, options={"comment": "A - ATIVO, I - INATIVO, R - REMOVIDO", "default": "I"})
     */
    private $situacao = "I";

    /**
     *
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    private $excluido = 0;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Token", mappedBy="usuario", cascade={"persist", "remove"})
     */
    private $token;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\UsuarioAcesso", mappedBy="usuario", cascade={"persist", "remove"})
     */
    private $usuarioAcesso;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $cpf;

    /**
     * @ORM\Column(type="boolean")
     */
    private $forca_troca_senha = false;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Franqueada", inversedBy="usuarios", fetch="LAZY")
     */
    private $franqueadas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada_padrao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Cheque", mappedBy="atendente_usuario", fetch="LAZY")
     */
    private $chequesAtendenteUsuario;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ContaReceber", mappedBy="usuario", fetch="LAZY")
     */
    private $usuarioContaReceber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ItemContaReceber", mappedBy="usuario_entregue", fetch="LAZY")
     */
    private $itemsEntreguesContaReceber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ModuloUsuarioAcao", mappedBy="usuario", fetch="LAZY")
     */
    private $moduloUsuarioAcaos;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Papel", inversedBy="usuariosPapeis", fetch="LAZY")
     */
    private $papels;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Recibo", mappedBy="usuario", fetch="LAZY")
     */
    private $recibosGerados;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FormularioFollowUp", mappedBy="usuario_alteracao", fetch="LAZY")
     */
    private $formularioFollowUps;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupComercial", mappedBy="usuario")
     */
    private $followupComercials;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Notificacoes", mappedBy="usuario")
     */
    private $notificacoes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FollowupConvenio", mappedBy="usuario")
     */
    private $followupConvenios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ChecklistAtividadeRealizada", mappedBy="usuario")
     */
    private $checklistAtividadeRealizadas;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AtividadeExtra", mappedBy="usuario_solicitante")
     */
    private $atividadeExtras;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoSituacaoAluno", mappedBy="usuario_alteracao")
     */
    private $historicoSituacaoAlunos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\OcorrenciaAcademica", mappedBy="usuario")
     */
    private $ocorrenciaAcademicas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Funcionario", mappedBy="usuario")
     */
    private $funcionarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ReposicaoAula", mappedBy="usuario_solicitante")
     */
    private $reposicaoAulas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\AgendaCompromisso", mappedBy="usuario")
     */
    private $agendaCompromissos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoItem", mappedBy="usuario")
     */
    private $historicoItems;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoEntregaItem", mappedBy="usuario_logado")
     */
    private $historicoEntregaItems;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\HistoricoEntregaItem", mappedBy="usuario_autorizou")
     */
    private $historicoEntregaItemsAutorizado;


    public function getId()
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSenha(): ?string
    {
        return $this->senha;
    }

    public function setSenha(string $senha): self
    {
        $this->senha = $senha;

        return $this;
    }

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(?\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;
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

    public function getExcluido(): ?bool
    {
        return $this->excluido;
    }

    public function setExcluido(bool $excluido): self
    {
        $this->excluido = $excluido;

        return $this;
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function setToken(Token $token): self
    {
        $this->token = $token;

        // set the owning side of the relation if necessary
        if ($this !== $token->getUsuario()) {
            $token->setUsuario($this);
        }

        return $this;
    }

    public function getUsuarioAcesso(): ?UsuarioAcesso
    {
        return $this->usuarioAcesso;
    }

    public function setUsuarioAcesso(UsuarioAcesso $usuarioAcesso): self
    {
        $this->usuarioAcesso = $usuarioAcesso;

        // set the owning side of the relation if necessary
        if ($this !== $usuarioAcesso->getUsuario()) {
            $usuarioAcesso->setUsuario($this);
        }

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getForcaTrocaSenha(): ?bool
    {
        return $this->forca_troca_senha;
    }

    public function setForcaTrocaSenha(bool $forca_troca_senha): self
    {
        $this->forca_troca_senha = $forca_troca_senha;

        return $this;
    }

    /**
     * Verifica se o usuario pertence a franqueadora
     *
     * @return bool|NULL
     */
    public function isUsuarioPertenceFranqueadora(): ?bool
    {
        $franqueadora = $this->franqueadas->filter(
            function (\App\Entity\Principal\Franqueada $franqueada) {
                return $franqueada->getFranqueadora() === true;
            }
        );

        return $franqueadora->count() > 0;
    }

    /**
     * @return Collection|Franqueada[]
     */
    public function getFranqueadas(): Collection
    {
        return $this->franqueadas;
    }

    public function limparFranqueadas()
    {
        $this->franqueadas = new ArrayCollection();
        return $this;
    }

    public function addFranqueada(Franqueada $franqueada): self
    {
        if ($this->franqueadas->contains($franqueada) === false) {
            $this->franqueadas[] = $franqueada;
        }

        return $this;
    }

    public function removeFranqueada(Franqueada $franqueada): self
    {
        if ($this->franqueadas->contains($franqueada) === true) {
            $this->franqueadas->removeElement($franqueada);
        }

        return $this;
    }

    public function getFranqueadaPadrao(): ?Franqueada
    {
        return $this->franqueada_padrao;
    }

    public function setFranqueadaPadrao(?Franqueada $franqueada_padrao): self
    {
        $this->franqueada_padrao = $franqueada_padrao;

        return $this;
    }

    /**
     * @return Collection|Cheque[]
     */
    public function getChequesAtendenteUsuario(): Collection
    {
        return $this->chequesAtendenteUsuario;
    }

    public function addChequesAtendenteUsuario(Cheque $chequesAtendenteUsuario): self
    {
        if ($this->chequesAtendenteUsuario->contains($chequesAtendenteUsuario) === false) {
            $this->chequesAtendenteUsuario[] = $chequesAtendenteUsuario;
            $chequesAtendenteUsuario->setAtendenteUsuario($this);
        }

        return $this;
    }

    public function removeChequesAtendenteUsuario(Cheque $chequesAtendenteUsuario): self
    {
        if ($this->chequesAtendenteUsuario->contains($chequesAtendenteUsuario) === true) {
            $this->chequesAtendenteUsuario->removeElement($chequesAtendenteUsuario);
            // set the owning side to null (unless already changed)
            if ($chequesAtendenteUsuario->getAtendenteUsuario() === $this) {
                $chequesAtendenteUsuario->setAtendenteUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ContaReceber[]
     */
    public function getUsuarioContaReceber(): Collection
    {
        return $this->usuarioContaReceber;
    }

    public function addUsuarioContaReceber(ContaReceber $usuarioContaReceber): self
    {
        if ($this->usuarioContaReceber->contains($usuarioContaReceber) === false) {
            $this->usuarioContaReceber[] = $usuarioContaReceber;
            $usuarioContaReceber->setUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioContaReceber(ContaReceber $usuarioContaReceber): self
    {
        if ($this->usuarioContaReceber->contains($usuarioContaReceber) === true) {
            $this->usuarioContaReceber->removeElement($usuarioContaReceber);
            // set the owning side to null (unless already changed)
            if ($usuarioContaReceber->getUsuario() === $this) {
                $usuarioContaReceber->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ItemContaReceber[]
     */
    public function getItemsEntreguesContaReceber(): Collection
    {
        return $this->itemsEntreguesContaReceber;
    }

    public function addItemsEntreguesContaReceber(ItemContaReceber $itemsEntregueContaReceber): self
    {
        if ($this->itemsEntregueContaReceber->contains($itemsEntregueContaReceber) === false) {
            $this->itemsEntregueContaReceber[] = $itemsEntregueContaReceber;
            $itemsEntregueContaReceber->setUsuarioEntregue($this);
        }

        return $this;
    }

    public function removeItemsEntreguesContaReceber(ItemContaReceber $itemsEntregueContaReceber): self
    {
        if ($this->itemsEntregueContaReceber->contains($itemsEntregueContaReceber) === true) {
            $this->itemsEntregueContaReceber->removeElement($itemsEntregueContaReceber);
            // set the owning side to null (unless already changed)
            if ($itemsEntregueContaReceber->getUsuarioEntregue() === $this) {
                $itemsEntregueContaReceber->setUsuarioEntregue(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ModuloUsuarioAcao[]
     */
    public function getModuloUsuarioAcaos(): Collection
    {
        return $this->moduloUsuarioAcaos;
    }

   
    public function limparModuloUsuarioAcaos()
    {
         $this->moduloUsuarioAcaos = new ArrayCollection();
         return $this;
    }


    public function addModuloUsuarioAcao(ModuloUsuarioAcao $moduloUsuarioAcao): self
    {
        if ($this->moduloUsuarioAcaos->contains($moduloUsuarioAcao) === false) {
            $this->moduloUsuarioAcaos[] = $moduloUsuarioAcao;
            $moduloUsuarioAcao->setUsuario($this);
        }

        return $this;
    }

    public function addModuloUsuarioAcaoList(Collection $modulos): self
    {
        $this->moduloUsuarioAcaos = new ArrayCollection();
        $this->moduloUsuarioAcaos = $modulos;
        return $this;
    }

    public function removeModuloUsuarioAcao(ModuloUsuarioAcao $moduloUsuarioAcao): self
    {
        if ($this->moduloUsuarioAcaos->contains($moduloUsuarioAcao) === true) {
            $this->moduloUsuarioAcaos->removeElement($moduloUsuarioAcao);
            // set the owning side to null (unless already changed)
            if ($moduloUsuarioAcao->getUsuario() === $this) {
                $moduloUsuarioAcao->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Papel[]
     */
    public function getPapels(): Collection
    {
        return $this->papels;
    }

    
    public function limparPapels()
    {
        $this->papels = new ArrayCollection();
        return $this;
    }
    

    public function addPapel(Papel $papel): self
    {
        if ($this->papels->contains($papel) === false) {
            $this->papels[] = $papel;
        }

        return $this;
    }

    public function removePapel(Papel $papel): self
    {
        if ($this->papels->contains($papel) === true) {
            $this->papels->removeElement($papel);
        }

        return $this;
    }

    /**
     * @return Collection|Recibo[]
     */
    public function getRecibosGerados(): Collection
    {
        return $this->recibosGerados;
    }

    public function addRecibosGerado(Recibo $recibosGerado): self
    {
        if ($this->recibosGerados->contains($recibosGerado) === false) {
            $this->recibosGerados[] = $recibosGerado;
            $recibosGerado->setUsuario($this);
        }

        return $this;
    }

    public function removeRecibosGerado(Recibo $recibosGerado): self
    {
        if ($this->recibosGerados->contains($recibosGerado) === true) {
            $this->recibosGerados->removeElement($recibosGerado);
            // set the owning side to null (unless already changed)
            if ($recibosGerado->getUsuario() === $this) {
                $recibosGerado->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FormularioFollowUp[]
     */
    public function getFormularioFollowUps(): Collection
    {
        return $this->formularioFollowUps;
    }

    public function addFormularioFollowUp(FormularioFollowUp $formularioFollowUp): self
    {
        if ($this->formularioFollowUps->contains($formularioFollowUp) === false) {
            $this->formularioFollowUps[] = $formularioFollowUp;
            $formularioFollowUp->setUsuarioAlteracao($this);
        }

        return $this;
    }

    public function removeFormularioFollowUp(FormularioFollowUp $formularioFollowUp): self
    {
        if ($this->formularioFollowUps->contains($formularioFollowUp) === true) {
            $this->formularioFollowUps->removeElement($formularioFollowUp);
            // set the owning side to null (unless already changed)
            if ($formularioFollowUp->getUsuarioAlteracao() === $this) {
                $formularioFollowUp->setUsuarioAlteracao(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FollowupComercial[]
     */
    public function getFollowupComercials(): Collection
    {
        return $this->followupComercials;
    }

    public function addFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === false) {
            $this->followupComercials[] = $followupComercial;
            $followupComercial->setUsuario($this);
        }

        return $this;
    }

    public function removeFollowupComercial(FollowupComercial $followupComercial): self
    {
        if ($this->followupComercials->contains($followupComercial) === true) {
            $this->followupComercials->removeElement($followupComercial);
            // set the owning side to null (unless already changed)
            if ($followupComercial->getUsuario() === $this) {
                $followupComercial->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notificacoes[]
     */
    public function getNotificacoes(): Collection
    {
        return $this->notificacoes;
    }

    public function addNotificaco(Notificacoes $notificaco): self
    {
        if ($this->notificacoes->contains($notificaco) === false) {
            $this->notificacoes[] = $notificaco;
            $notificaco->addUsuario($this);
        }

        return $this;
    }

    /**
     * @return Collection|FollowupConvenio[]
     */
    public function getFollowupConvenios(): Collection
    {
        return $this->followupConvenios;
    }

    public function addFollowupConvenio(FollowupConvenio $followupConvenio): self
    {
        if ($this->followupConvenios->contains($followupConvenio) === false) {
            $this->followupConvenios[] = $followupConvenio;
            $followupConvenio->setUsuario($this);
        }

        return $this;
    }

    public function removeNotificaco(Notificacoes $notificaco): self
    {
        if ($this->notificacoes->contains($notificaco) === true) {
            $this->notificacoes->removeElement($notificaco);
            $notificaco->removeUsuario($this);
        }

        return $this;
    }

    public function removeFollowupConvenio(FollowupConvenio $followupConvenio): self
    {
        if ($this->followupConvenios->contains($followupConvenio) === true) {
            $this->followupConvenios->removeElement($followupConvenio);
            // set the owning side to null (unless already changed)
            if ($followupConvenio->getUsuario() === $this) {
                $followupConvenio->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ChecklistAtividadeRealizada[]
     */
    public function getChecklistAtividadeRealizadas(): Collection
    {
        return $this->checklistAtividadeRealizadas;
    }

    public function addChecklistAtividadeRealizada(ChecklistAtividadeRealizada $checklistAtividadeRealizada): self
    {
        if ($this->checklistAtividadeRealizadas->contains($checklistAtividadeRealizada) === false) {
            $this->checklistAtividadeRealizadas[] = $checklistAtividadeRealizada;
            $checklistAtividadeRealizada->setUsuario($this);
        }

        return $this;
    }

    public function removeChecklistAtividadeRealizada(ChecklistAtividadeRealizada $checklistAtividadeRealizada): self
    {
        if ($this->checklistAtividadeRealizadas->contains($checklistAtividadeRealizada) === true) {
            $this->checklistAtividadeRealizadas->removeElement($checklistAtividadeRealizada);
            // set the owning side to null (unless already changed)
            if ($checklistAtividadeRealizada->getUsuario() === $this) {
                $checklistAtividadeRealizada->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|AtividadeExtra[]
     */
    public function getAtividadeExtras(): Collection
    {
        return $this->atividadeExtras;
    }

    public function addAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === false) {
            $this->atividadeExtras[] = $atividadeExtra;
            $atividadeExtra->setUsuarioSolicitante($this);
        }

        return $this;
    }

    public function removeAtividadeExtra(AtividadeExtra $atividadeExtra): self
    {
        if ($this->atividadeExtras->contains($atividadeExtra) === true) {
            $this->atividadeExtras->removeElement($atividadeExtra);
            // set the owning side to null (unless already changed)
            if ($atividadeExtra->getUsuarioSolicitante() === $this) {
                $atividadeExtra->setUsuarioSolicitante(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|HistoricoSituacaoAluno[]
     */
    public function getHistoricoSituacaoAlunos(): Collection
    {
        return $this->historicoSituacaoAlunos;
    }

    public function addHistoricoSituacaoAluno(HistoricoSituacaoAluno $historicoSituacaoAluno): self
    {
        if ($this->historicoSituacaoAlunos->contains($historicoSituacaoAluno) === false) {
            $this->historicoSituacaoAlunos[] = $historicoSituacaoAluno;
            $historicoSituacaoAluno->setUsuarioAlteracao($this);
        }

        return $this;
    }

    public function removeHistoricoSituacaoAluno(HistoricoSituacaoAluno $historicoSituacaoAluno): self
    {
        if ($this->historicoSituacaoAlunos->contains($historicoSituacaoAluno) === true) {
            $this->historicoSituacaoAlunos->removeElement($historicoSituacaoAluno);
            // set the owning side to null (unless already changed)
            if ($historicoSituacaoAluno->getUsuarioAlteracao() === $this) {
                $historicoSituacaoAluno->setUsuarioAlteracao(null);
            }
        }

        return $this;
    }

    /**
     *
     * @return Collection|Funcionario[]
     */
    public function getFuncionarios(): Collection
    {
        return $this->funcionarios;
    }

    public function addFuncionario(Funcionario $funcionario): self
    {
        if ($this->funcionarios->contains($funcionario) === false) {
            $this->funcionarios[] = $funcionario;
            $funcionario->setUsuario($this);
        }

        return $this;
    }

    public function removeFuncionario(Funcionario $funcionario): self
    {
        if ($this->funcionarios->contains($funcionario) === true) {
            $this->funcionarios->removeElement($funcionario);
            // set the owning side to null (unless already changed)
            if ($funcionario->getUsuario() === $this) {
                $funcionario->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OcorrenciaAcademica[]
     */
    public function getOcorrenciaAcademicas(): Collection
    {
        return $this->ocorrenciaAcademicas;
    }

    public function addOcorrenciaAcademica(OcorrenciaAcademica $ocorrenciaAcademica): self
    {
        if ($this->ocorrenciaAcademicas->contains($ocorrenciaAcademica) === false) {
            $this->ocorrenciaAcademicas[] = $ocorrenciaAcademica;
            $ocorrenciaAcademica->setUsuario($this);
        }

        return $this;
    }

    public function removeOcorrenciaAcademica(OcorrenciaAcademica $ocorrenciaAcademica): self
    {
        if ($this->ocorrenciaAcademicas->contains($ocorrenciaAcademica) === true) {
            $this->ocorrenciaAcademicas->removeElement($ocorrenciaAcademica);
            // set the owning side to null (unless already changed)
            if ($ocorrenciaAcademica->getUsuario() === $this) {
                $ocorrenciaAcademica->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReposicaoAula[]
     */
    public function getReposicaoAulas(): Collection
    {
        return $this->reposicaoAulas;
    }

    public function addReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === false) {
            $this->reposicaoAulas[] = $reposicaoAula;
            $reposicaoAula->setUsuarioSolicitante($this);
        }

        return $this;
    }

    public function removeReposicaoAula(ReposicaoAula $reposicaoAula): self
    {
        if ($this->reposicaoAulas->contains($reposicaoAula) === true) {
            $this->reposicaoAulas->removeElement($reposicaoAula);
            // set the owning side to null (unless already changed)
            if ($reposicaoAula->getUsuarioSolicitante() === $this) {
                $reposicaoAula->setUsuarioSolicitante(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|Token[]
     */
    public function getTokens(): Collection
    {
        return $this->token;
    }

    public function addToken(Token $token): self
    {
        if ($this->token->contains($token) === false) {
            $this->token[] = $token;
            $token->setAluno($this);
        }

        return $this;
    }

    public function removeToken(Token $token): self
    {
        if ($this->token->contains($token) === true) {
            $this->token->removeElement($token);
            // set the owning side to null (unless already changed)
            if ($token->getAluno() === $this) {
                $token->setAluno(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AgendaCompromisso[]
     */
    public function getAgendaCompromissos(): Collection
    {
        return $this->agendaCompromissos;
    }

    public function addAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === false) {
            $this->agendaCompromissos[] = $agendaCompromisso;
            $agendaCompromisso->setUsuario($this);
        }

        return $this;
    }

    public function removeAgendaCompromisso(AgendaCompromisso $agendaCompromisso): self
    {
        if ($this->agendaCompromissos->contains($agendaCompromisso) === true) {
            $this->agendaCompromissos->removeElement($agendaCompromisso);
            // set the owning side to null (unless already changed)
            if ($agendaCompromisso->getUsuario() === $this) {
                $agendaCompromisso->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoItem[]
     */
    public function getHistoricoItems(): Collection
    {
        return $this->historicoItems;
    }

    public function addHistoricoItem(HistoricoItem $historicoItem): self
    {
        if ($this->historicoItems->contains($historicoItem) === false) {
            $this->historicoItems[] = $historicoItem;
            $historicoItem->setUsuario($this);
        }

        return $this;
    }

    public function removeHistoricoItem(HistoricoItem $historicoItem): self
    {
        if ($this->historicoItems->contains($historicoItem) === true) {
            $this->historicoItems->removeElement($historicoItem);
            // set the owning side to null (unless already changed)
            if ($historicoItem->getUsuario() === $this) {
                $historicoItem->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoEntregaItem[]
     */
    public function getHistoricoEntregaItems(): Collection
    {
        return $this->historicoEntregaItems;
    }

    public function addHistoricoEntregaItem(HistoricoEntregaItem $historicoEntregaItem): self
    {
        if ($this->historicoEntregaItems->contains($historicoEntregaItem) === false) {
            $this->historicoEntregaItems[] = $historicoEntregaItem;
            $historicoEntregaItem->setUsuarioLogado($this);
        }

        return $this;
    }

    public function removeHistoricoEntregaItem(HistoricoEntregaItem $historicoEntregaItem): self
    {
        if ($this->historicoEntregaItems->contains($historicoEntregaItem) === true) {
            $this->historicoEntregaItems->removeElement($historicoEntregaItem);
            // set the owning side to null (unless already changed)
            if ($historicoEntregaItem->getUsuarioLogado() === $this) {
                $historicoEntregaItem->setUsuarioLogado(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HistoricoEntregaItem[]
     */
    public function getHistoricoEntregaItemsAutorizado(): Collection
    {
        return $this->historicoEntregaItemsAutorizado;
    }

    public function addHistoricoEntregaItemsAutorizado(HistoricoEntregaItem $historicoEntregaItemsAutorizado): self
    {
        if ($this->historicoEntregaItemsAutorizado->contains($historicoEntregaItemsAutorizado) === false) {
            $this->historicoEntregaItemsAutorizado[] = $historicoEntregaItemsAutorizado;
            $historicoEntregaItemsAutorizado->setUsuarioAutorizou($this);
        }

        return $this;
    }

    public function removeHistoricoEntregaItemsAutorizado(HistoricoEntregaItem $historicoEntregaItemsAutorizado): self
    {
        if ($this->historicoEntregaItemsAutorizado->contains($historicoEntregaItemsAutorizado) === true) {
            $this->historicoEntregaItemsAutorizado->removeElement($historicoEntregaItemsAutorizado);
            // set the owning side to null (unless already changed)
            if ($historicoEntregaItemsAutorizado->getUsuarioAutorizou() === $this) {
                $historicoEntregaItemsAutorizado->setUsuarioAutorizou(null);
            }
        }

        return $this;
    }


}
