<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\EmailConfiguracaoRepository")
 * @ORM\Table(name="email_configuracao")
 */
class EmailConfiguracao
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
    private $host;

    /**
     * @ORM\Column(type="integer")
     */
    private $port;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $encryption;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_smtp = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $from_nome;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $from_email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getEncryption(): ?string
    {
        return $this->encryption;
    }

    public function setEncryption(string $encryption): self
    {
        $this->encryption = $encryption;

        return $this;
    }

    public function getIsSmtp(): ?bool
    {
        return $this->is_smtp;
    }

    public function setIsSmtp(bool $is_smtp): self
    {
        $this->is_smtp = $is_smtp;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFromNome(): ?string
    {
        return $this->from_nome;
    }

    public function setFromNome(string $from_nome): self
    {
        $this->from_nome = $from_nome;

        return $this;
    }

    public function getFromEmail(): ?string
    {
        return $this->from_email;
    }

    public function setFromEmail(string $from_email): self
    {
        $this->from_email = $from_email;

        return $this;
    }


}
