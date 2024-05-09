<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FormularioFollowUpCamposRepository")
 * @ORM\Table(name="formulario_follow_up_campos")
 */
class FormularioFollowUpCampos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\FormularioFollowUp", inversedBy="formularioFollowUpCampos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formulario_follow_up;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome_campo;

    /**
     * @ORM\Column(type="boolean", options={"default": false} )
     */
    private $texto_longo = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormularioFollowUp(): ?FormularioFollowUp
    {
        return $this->formulario_follow_up;
    }

    public function setFormularioFollowUp(?FormularioFollowUp $formulario_follow_up): self
    {
        $this->formulario_follow_up = $formulario_follow_up;

        return $this;
    }

    public function getNomeCampo(): ?string
    {
        return $this->nome_campo;
    }

    public function setNomeCampo(string $nome_campo): self
    {
        $this->nome_campo = $nome_campo;

        return $this;
    }

    public function getTextoLongo(): ?bool
    {
        return $this->texto_longo;
    }

    public function setTextoLongo(bool $texto_longo): self
    {
        $this->texto_longo = $texto_longo;

        return $this;
    }


}
