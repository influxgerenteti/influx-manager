<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\NotificacoesRepository")
 */
class Notificacoes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Usuario", inversedBy="notificacoes")
     */
    private $usuario;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Franqueada", inversedBy="notificacoes")
     */
    private $franqueada;

    /**
     * @ORM\Column(type="text")
     */
    private $mensagem;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_notificacao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_prorrogacao;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_lida;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $class_css;

    public function __construct()
    {
        $this->usuario    = new ArrayCollection();
        $this->franqueada = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuario(): Collection
    {
        return $this->usuario;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if ($this->usuario->contains($usuario) === false) {
            $this->usuario[] = $usuario;
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuario->contains($usuario) === true) {
            $this->usuario->removeElement($usuario);
        }

        return $this;
    }

    /**
     * @return Collection|Franqueada[]
     */
    public function getFranqueada(): Collection
    {
        return $this->franqueada;
    }

    public function addFranqueada(Franqueada $franqueada): self
    {
        if ($this->franqueada->contains($franqueada) === false) {
            $this->franqueada[] = $franqueada;
        }

        return $this;
    }

    public function removeFranqueada(Franqueada $franqueada): self
    {
        if ($this->franqueada->contains($franqueada) === true) {
            $this->franqueada->removeElement($franqueada);
        }

        return $this;
    }

    public function getMensagem(): ?string
    {
        return $this->mensagem;
    }

    public function setMensagem(string $mensagem): self
    {
        $this->mensagem = $mensagem;

        return $this;
    }

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    public function getDataNotificacao(): ?\DateTimeInterface
    {
        return $this->data_notificacao;
    }

    public function setDataNotificacao(?\DateTimeInterface $data_notificacao): self
    {
        $this->data_notificacao = $data_notificacao;

        return $this;
    }

    public function getDataProrrogacao(): ?\DateTimeInterface
    {
        return $this->data_prorrogacao;
    }

    public function setDataProrrogacao(?\DateTimeInterface $data_prorrogacao): self
    {
        $this->data_prorrogacao = $data_prorrogacao;

        return $this;
    }

    public function getIsLida(): ?bool
    {
        return $this->is_lida;
    }

    public function setIsLida(bool $is_lida): self
    {
        $this->is_lida = $is_lida;

        return $this;
    }

    public function getClassCss(): ?string
    {
        return $this->class_css;
    }

    public function setClassCss(?string $class_css): self
    {
        $this->class_css = $class_css;

        return $this;
    }


}
