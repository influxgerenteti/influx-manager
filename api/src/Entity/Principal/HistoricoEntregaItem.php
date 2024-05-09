<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\HistoricoEntregaItemRepository")
 */
class HistoricoEntregaItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="historicoEntregaItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario_logado;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="historicoEntregaItemsAutorizado")
     */
    private $usuario_autorizou;

    /**
     * @ORM\Column(type="string", length=1, options={"comment":"E - Entregue, N - Nao entregue, C - Cancelado"})
     */
    private $situacao;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data_acontecimento;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuarioLogado(): ?Usuario
    {
        return $this->usuario_logado;
    }

    public function setUsuarioLogado(?Usuario $usuario_logado): self
    {
        $this->usuario_logado = $usuario_logado;

        return $this;
    }

    public function getUsuarioAutorizou(): ?Usuario
    {
        return $this->usuario_autorizou;
    }

    public function setUsuarioAutorizou(?Usuario $usuario_autorizou): self
    {
        $this->usuario_autorizou = $usuario_autorizou;

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

    public function getDataAcontecimento(): ?\DateTimeInterface
    {
        return $this->data_acontecimento;
    }

    public function setDataAcontecimento(\DateTimeInterface $data_acontecimento): self
    {
        $this->data_acontecimento = $data_acontecimento;

        return $this;
    }


}
