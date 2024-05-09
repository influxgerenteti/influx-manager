<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FormularioFollowUpRepository")
 * @ORM\Table(name="formulario_follow_up")
 */
class FormularioFollowUp
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao_formulario;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_ultima_alteracao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="formularioFollowUps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario_alteracao;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"A- ATIVO, I - INATIVO"})
     */
    private $situacao = 'A';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\FormularioFollowUpCampos", mappedBy="formulario_follow_up")
     */
    private $formularioFollowUpCampos;

    /**
     * @ORM\Column(type="string", length=2, options={"comment":"CA - CONTATO ATIVO, CR - CONTATO RECEPTIVO, NP - NEGOCIACAO DE PARCEIRIA, NI - NIVELAMENTO"})
     */
    private $tipo_formulario;

    /**
     * @ORM\Column(type="boolean")
     */
    private $follow_up_inicial;

    public function __construct()
    {
        $this->formularioFollowUpCampos = new ArrayCollection();
        $this->data_ultima_alteracao    = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescricaoFormulario(): ?string
    {
        return $this->descricao_formulario;
    }

    public function setDescricaoFormulario(string $descricao_formulario): self
    {
        $this->descricao_formulario = $descricao_formulario;

        return $this;
    }

    public function getDataUltimaAlteracao(): ?\DateTimeInterface
    {
        return $this->data_ultima_alteracao;
    }

    public function setDataUltimaAlteracao(?\DateTimeInterface $data_ultima_alteracao): self
    {
        $this->data_ultima_alteracao = $data_ultima_alteracao;

        return $this;
    }

    public function getUsuarioAlteracao(): ?Usuario
    {
        return $this->usuario_alteracao;
    }

    public function setUsuarioAlteracao(?Usuario $usuario_alteracao): self
    {
        $this->usuario_alteracao = $usuario_alteracao;

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

    /**
     * @return Collection|FormularioFollowUpCampos[]
     */
    public function getFormularioFollowUpCampos(): Collection
    {
        return $this->formularioFollowUpCampos;
    }

    public function addFormularioFollowUpCampo(FormularioFollowUpCampos $formularioFollowUpCampo): self
    {
        if ($this->formularioFollowUpCampos->contains($formularioFollowUpCampo) === false) {
            $this->formularioFollowUpCampos[] = $formularioFollowUpCampo;
            $formularioFollowUpCampo->setFormularioFollowUp($this);
        }

        return $this;
    }

    public function removeFormularioFollowUpCampo(FormularioFollowUpCampos $formularioFollowUpCampo): self
    {
        if ($this->formularioFollowUpCampos->contains($formularioFollowUpCampo) === true) {
            $this->formularioFollowUpCampos->removeElement($formularioFollowUpCampo);
            // set the owning side to null (unless already changed)
            if ($formularioFollowUpCampo->getFormularioFollowUp() === $this) {
                $formularioFollowUpCampo->setFormularioFollowUp(null);
            }
        }

        return $this;
    }


    public function getTipoFormulario(): ?string
    {
        return $this->tipo_formulario;
    }

    public function setTipoFormulario(string $tipo_formulario): self
    {
        $this->tipo_formulario = $tipo_formulario;

        return $this;
    }

    public function getFollowUpInicial(): ?bool
    {
        return $this->follow_up_inicial;
    }

    public function setFollowUpInicial(bool $follow_up_inicial): self
    {
        $this->follow_up_inicial = $follow_up_inicial;

        return $this;
    }


}
