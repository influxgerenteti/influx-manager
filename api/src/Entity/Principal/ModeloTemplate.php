<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ModeloTemplateRepository")
 * @ORM\Table(name="modelo_template")
 */
class ModeloTemplate
{


    public function __construct()
    {
        $this->modelo_template_franqueadas = new ArrayCollection();
    }
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="modeloTemplates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\Column(type="text")
     */
    private $modelo_html;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"A - Ativo, I - Inativo", "default":"A"})
     */
    private $situacao = 'A';

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $tipo_template;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ModeloTemplateFranqueada", mappedBy="modelo_template")
     */
    private $modelo_template_franqueadas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranqueada(): ?Franqueada
    {
        return $this->franqueada;
    }

    public function setFranqueada(?Franqueada $franqueada): self
    {
        $this->franqueada = $franqueada;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getModeloHtml(): ?string
    {
        return $this->modelo_html;
    }

    public function setModeloHtml(string $modelo_html): self
    {
        $this->modelo_html = $modelo_html;

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

    public function getTipoTemplate(): ?string
    {
        return $this->tipo_template;
    }

    public function setTipoTemplate(string $tipo_template): self
    {
        $this->tipo_template = $tipo_template;

        return $this;
    }


    /**
     * @return Collection|ModeloTemplateFranqueada[]
     */
    public function getModeloTemplateFranqueadas(): Collection
    {
        return $this->modelo_template_franqueadas;
    }

    public function addModeloTemplateFranqueada(ModeloTemplateFranqueada $modeloTemplateFranqueada): self
    {
        if ($this->modelo_template_franqueadas->contains($modeloTemplateFranqueada) === false) {
            $this->modelo_template_franqueadas[] = $modeloTemplateFranqueada;
            $modeloTemplateFranqueada->setModeloTemplate($this);
        }

        return $this;
    }

    public function removeModeloTemplateFranqueada(ModeloTemplateFranqueada $modeloTemplateFranqueada): self
    {
        if ($this->modelo_template_franqueadas->contains($modeloTemplateFranqueada) === true) {
            $this->modelo_template_franqueadas->removeElement($modeloTemplateFranqueada);
        }

        return $this;
    }


}
