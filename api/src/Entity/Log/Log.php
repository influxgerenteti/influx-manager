<?php
namespace App\Entity\Log;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Log\LogRepository")
 */
class Log
{


    public function __construct()
    {
        $this->data = new \DateTime();
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
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $ip_origem;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $info_evento;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $modulo;

    /**
     *
     * @ORM\Column(type="string", length=1, options={"comment":"L - LOGIN, A - ACESSO, C - CRIADO, R - LEITURA, U - ATULIZAÇÃO, D - EXCLUSÃO"})
     */
    private $tipo_evento;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $usuario;

    /**
     *
     * @ORM\Column(type="string", name="usuario_nome", nullable=true)
     */
    private $usuario_nome;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $franqueada;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $dados_anteriores;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $dados_atuais;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tabela;

    public function getId()
    {
        return $this->id;
    }

    public function getData(): ?\DateTimeInterface
    {
        return $this->data;
    }

    public function setData(\DateTimeInterface $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getIpOrigem(): ?string
    {
        return $this->ip_origem;
    }

    public function setIpOrigem(string $ip_origem): self
    {
        $this->ip_origem = $ip_origem;

        return $this;
    }

    public function getInfoEvento()
    {
        return $this->info_evento;
    }

    public function setInfoEvento($info_evento): self
    {
        $this->info_evento = $info_evento;

        return $this;
    }

    public function getTipoEvento(): ?string
    {
        return $this->tipo_evento;
    }

    public function setTipoEvento(?string $tipo_evento): self
    {
        $this->tipo_evento = $tipo_evento;

        return $this;
    }

    public function getUsuario(): ?int
    {
        return $this->usuario;
    }

    public function setUsuario(?int $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getFranqueada(): ?int
    {
        return $this->franqueada;
    }

    public function setFranqueada(?int $franqueada): self
    {
        $this->franqueada = $franqueada;

        return $this;
    }

    public function getDadosAnteriores(): ?string
    {
        return $this->dados_anteriores;
    }

    public function setDadosAnteriores(?string $dados_anteriores): self
    {
        $this->dados_anteriores = $dados_anteriores;

        return $this;
    }

    public function getDadosAtuais(): ?string
    {
        return $this->dados_atuais;
    }

    public function setDadosAtuais(?string $dados_atuais): self
    {
        $this->dados_atuais = $dados_atuais;

        return $this;
    }

    public function getTabela(): ?string
    {
        return $this->tabela;
    }

    public function setTabela(?string $tabela): self
    {
        $this->tabela = $tabela;

        return $this;
    }

    /**
     * Get the value of usuario_nome
     */
    public function getUsuarioNome()
    {
        return $this->usuario_nome;
    }

    /**
     * Set the value of usuario_nome
     */
    public function setUsuarioNome($usuario_nome): self
    {
        $this->usuario_nome = $usuario_nome;

        return $this;
    }

    /**
     * Get the value of modulo
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * Set the value of modulo
     */
    public function setModulo($modulo): self
    {
        $this->modulo = $modulo;

        return $this;
    }
}
