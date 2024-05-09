<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ValorHoraLinhasRepository")
 * @ORM\Table(name="valor_hora_linhas")
 */
class ValorHoraLinhas
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=1, options={"default":"A","comment":"A - Ativo, I - Inativo"})
     */
    private $situacao = 'A';

    /**
     * @ORM\Column(type="string", length=3, options={"comment":"(TUR)mas, VIP, (PER)sonal, (BON)us Class, (ATI)vidade Extra"})
     */
    private $tipo;

    /**
     * @ORM\Column(type="integer", options={"default":1})
     */
    private $quantidade_alunos_minima;

    /**
     * @ORM\Column(type="integer", options={"default":1})
     */
    private $quantidade_alunos_maxima;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"(H)orista, (M)ensalista"})
     */
    private $tipo_pagamento = 'M';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getQuantidadeAlunosMinima(): ?int
    {
        return $this->quantidade_alunos_minima;
    }

    public function setQuantidadeAlunosMinima(int $quantidade_alunos_minima): self
    {
        $this->quantidade_alunos_minima = $quantidade_alunos_minima;

        return $this;
    }

    public function getQuantidadeAlunosMaxima(): ?int
    {
        return $this->quantidade_alunos_maxima;
    }

    public function setQuantidadeAlunosMaxima(int $quantidade_alunos_maxima): self
    {
        $this->quantidade_alunos_maxima = $quantidade_alunos_maxima;

        return $this;
    }

    public function getTipoPagamento(): ?string
    {
        return $this->tipo_pagamento;
    }

    public function setTipoPagamento(string $tipo_pagamento): self
    {
        $this->tipo_pagamento = $tipo_pagamento;

        return $this;
    }


}
