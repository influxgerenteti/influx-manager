<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ArquivosRepository")
 */
class Arquivos
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
    private $nome_arquivo;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $extensao;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $caminho_servidor;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_criacao;

    /**
     * @ORM\Column(type="boolean")
     */
    private $excluido;

    function __construct()
    {
        $this->data_criacao = new \DateTime();
        $this->excluido     = false;
    }

    public function getNomeArquivo(): ?string
    {
        return $this->nome_arquivo;
    }

    public function setNomeArquivo(string $nome_arquivo): self
    {
        $this->nome_arquivo = $nome_arquivo;

        return $this;
    }

    public function getExtensao(): ?string
    {
        return $this->extensao;
    }

    public function setExtensao(string $extensao): self
    {
        $this->extensao = $extensao;

        return $this;
    }

    public function getCaminhoServidor(): ?string
    {
        return $this->caminho_servidor;
    }

    public function setCaminhoServidor(string $caminho_servidor): self
    {
        $this->caminho_servidor = $caminho_servidor;

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

    public function getExcluido(): ?bool
    {
        return $this->excluido;
    }

    public function setExcluido(bool $excluido): self
    {
        $this->excluido = $excluido;

        return $this;
    }


}
