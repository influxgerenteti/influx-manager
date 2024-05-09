<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FollowupConvenioRepository")
 * @ORM\Table(name="followup_convenio")
 */
class FollowupConvenio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Convenio", inversedBy="followupConvenios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $convenio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="followupConvenios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\TipoContato", inversedBy="followupConvenios")
     */
    private $tipo_contato;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_registro;

    /**
     * @ORM\Column(type="text")
     */
    private $followup;

    function __construct()
    {
        $this->data_registro = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConvenio(): ?Convenio
    {
        return $this->convenio;
    }

    public function setConvenio(?Convenio $convenio): self
    {
        $this->convenio = $convenio;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getTipoContato(): ?TipoContato
    {
        return $this->tipo_contato;
    }

    public function setTipoContato(?TipoContato $tipo_contato): self
    {
        $this->tipo_contato = $tipo_contato;

        return $this;
    }

    public function getDataRegistro(): ?\DateTimeInterface
    {
        return $this->data_registro;
    }

    public function setDataRegistro(\DateTimeInterface $data_registro): self
    {
        $this->data_registro = $data_registro;

        return $this;
    }

    public function getFollowUp(): ?string
    {
        return $this->followup;
    }

    public function setFollowUp(string $followup): self
    {
        $this->followup = $followup;

        return $this;
    }


}
