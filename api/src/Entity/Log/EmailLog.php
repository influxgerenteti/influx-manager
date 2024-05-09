<?php

namespace App\Entity\Log;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Log\EmailLogRepository")
 * @ORM\Table(name="email_log")
 */
class EmailLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $usuario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_envio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $assunto;

    /**
     * @ORM\Column(type="text")
     */
    private $email_corpo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $erro_php;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ocorreu_erro = false;

    /**
     * @ORM\Column(type="text")
     */
    private $lista_email_envio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?int
    {
        return $this->usuario;
    }

    public function setUsuario(int $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getDataEnvio(): ?\DateTimeInterface
    {
        return $this->data_envio;
    }

    public function setDataEnvio(\DateTimeInterface $data_envio): self
    {
        $this->data_envio = $data_envio;

        return $this;
    }

    public function getAssunto(): ?string
    {
        return $this->assunto;
    }

    public function setAssunto(string $assunto): self
    {
        $this->assunto = $assunto;

        return $this;
    }

    public function getEmailCorpo(): ?string
    {
        return $this->email_corpo;
    }

    public function setEmailCorpo(string $email_corpo): self
    {
        $this->email_corpo = $email_corpo;

        return $this;
    }

    public function getErroPhp(): ?string
    {
        return $this->erro_php;
    }

    public function setErroPhp(?string $erro_php): self
    {
        $this->erro_php = $erro_php;

        return $this;
    }

    public function getOcorreuErro(): ?bool
    {
        return $this->ocorreu_erro;
    }

    public function setOcorreuErro(bool $ocorreu_erro): self
    {
        $this->ocorreu_erro = $ocorreu_erro;

        return $this;
    }

    public function getListaEmailEnvio(): ?string
    {
        return $this->lista_email_envio;
    }

    public function setListaEmailEnvio(string $lista_email_envio): self
    {
        $this->lista_email_envio = $lista_email_envio;

        return $this;
    }


}
