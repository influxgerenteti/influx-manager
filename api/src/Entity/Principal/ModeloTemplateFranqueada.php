<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ModeloTemplateFranqueadaRepository")
 */
class ModeloTemplateFranqueada
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="modelo_template_franqueadas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ModeloTemplate", inversedBy="modelo_template_franqueadas")
     * @ORM\JoinColumn(nullable=true)
     */
    private $modelo_template;

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


    public function getModeloTemplate(): ?ModeloTemplate
    {
        return $this->modelo_template;
    }

    public function setModeloTemplate(ModeloTemplate $modelo_template): self
    {
        $this->modelo_template = $modelo_template;

        return $this;
    }


}
