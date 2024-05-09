<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\HistoricoItemRepository")
 */
class HistoricoItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="historicoItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="historicoItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\Column(type="blob")
     */
    private $log;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_alteracao;

    public function __construct()
    {
        $this->data_alteracao = new \DateTime();
    }

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

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function setLog($log): self
    {
        $this->log = $log;

        return $this;
    }

    public function getDataAlteracao(): ?\DateTimeInterface
    {
        return $this->data_alteracao;
    }

    public function setDataAlteracao(\DateTimeInterface $data_alteracao): self
    {
        $this->data_alteracao = $data_alteracao;

        return $this;
    }


}
