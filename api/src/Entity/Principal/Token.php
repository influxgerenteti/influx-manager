<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\TokenRepository")
 */
class Token
{


    public function __construct()
    {
        $this->data_solicitacao = new \DateTime();
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
     * @ORM\Column(type="boolean", nullable=true, options={"default": "1"})
     */
    private $email_enviado;

    /**
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_solicitacao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="token")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_expirado;

    public function getId()
    {
        return $this->id;
    }

    public function getEmailEnviado(): ?bool
    {
        return $this->email_enviado;
    }

    public function setEmailEnviado(?bool $email_enviado): self
    {
        $this->email_enviado = $email_enviado;

        return $this;
    }

    public function getDataSolicitacao(): ?\DateTimeInterface
    {
        return $this->data_solicitacao;
    }

    public function setDataSolicitacao(\DateTimeInterface $data_solicitacao): self
    {
        $this->data_solicitacao = $data_solicitacao;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getDataExpirado(): ?\DateTimeInterface
    {
        return $this->data_expirado;
    }

    public function setDataExpirado(\DateTimeInterface $data_expirado): self
    {
        $this->data_expirado = $data_expirado;

        return $this;
    }


}
