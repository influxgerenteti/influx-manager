<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\MensagensAjudaRepository")
 * @ORM\Table(name="mensagens_ajuda",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="indice_relacional",
 *              columns={"modulo_id", "identificador_elemento"}
 *          )
 *      }
 * )
 * */
class MensagensAjuda
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Modulo")
     */
    private $modulo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identificador_elemento;

    /**
     * @ORM\Column(type="text")
     */
    private $mensagem;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModulo(): ?Modulo
    {
        return $this->modulo;
    }

    public function setModulo(?Modulo $modulo): self
    {
        $this->modulo = $modulo;

        return $this;
    }

    public function getIdentificadorElemento(): ?string
    {
        return $this->identificador_elemento;
    }

    public function setIdentificadorElemento(string $identificador_elemento): self
    {
        $this->identificador_elemento = $identificador_elemento;

        return $this;
    }

    public function getMensagem()
    {
        return $this->mensagem;
    }

    public function setMensagem($mensagem): self
    {
        $this->mensagem = $mensagem;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(?string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }


}
