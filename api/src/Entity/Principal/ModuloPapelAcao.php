<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ModuloPapelAcaoRepository")
 * @ORM\Table(name="modulo_papel_acao",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="indice_relacional",
 *              columns={"modulo_id", "papel_id", "acao_sistema_id"}
 *          )
 *      }
 * )
 */
class ModuloPapelAcao
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Modulo", inversedBy="modulo_papel_acao", fetch="LAZY")
     * @ORM\JoinColumn(name="modulo_id",                          referencedColumnName="id", nullable=false)
     */
    private $modulo;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Papel", inversedBy="moduloPapelAcao", fetch="LAZY")
     * @ORM\JoinColumn(name="papel_id",                          referencedColumnName="id", nullable=false)
     */
    private $papel;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AcaoSistema", inversedBy="moduloPapelAcao", fetch="LAZY")
     * @ORM\JoinColumn(name="acao_sistema_id",                         referencedColumnName="id", nullable=false)
     */
    private $acao_sistema;

    public function getModulo(): ?Modulo
    {
        return $this->modulo;
    }

    public function setModulo(?Modulo $modulo): self
    {
        $this->modulo = $modulo;

        return $this;
    }

    public function getPapel(): ?Papel
    {
        return $this->papel;
    }

    public function setPapel(?Papel $papel): self
    {
        $this->papel = $papel;

        return $this;
    }

    public function getAcaoSistema(): ?AcaoSistema
    {
        return $this->acao_sistema;
    }

    public function setAcaoSistema(?AcaoSistema $acao_sistema): self
    {
        $this->acao_sistema = $acao_sistema;

        return $this;
    }


}
