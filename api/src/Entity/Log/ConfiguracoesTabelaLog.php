<?php

namespace App\Entity\Log;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Log\ConfiguracoesTabelaLogRepository")
 * @ORM\Table(name="configuracoes_tabela_log")
 */
class ConfiguracoesTabelaLog
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
    private $nome_tabela;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ativo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomeTabela(): ?string
    {
        return $this->nome_tabela;
    }

    public function setNomeTabela(string $nome_tabela): self
    {
        $this->nome_tabela = $nome_tabela;

        return $this;
    }

    public function getAtivo(): ?bool
    {
        return $this->ativo;
    }

    public function setAtivo(bool $ativo): self
    {
        $this->ativo = $ativo;

        return $this;
    }


}
