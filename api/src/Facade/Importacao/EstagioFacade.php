<?php

namespace App\Facade\Importacao;


use App\BO\Importacao\EstagioBO;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Importacao\Estagio;
use App\Entity\Principal\Franqueada;
use App\Facade\Principal\GenericFacade;
use App\Entity\Importacao\Curso;
use App\Entity\Importacao\Item;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class EstagioFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\EstagioRepository
     */
    private $estagioRepository;

    /**
     *
     * @var \App\Repository\Importacao\ItemRepository
     */
    private $itemRepository;

    /**
     *
     * @var \App\Repository\Importacao\CursoRepository
     */
    private $cursoRepository;

    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $foundationEntityManager;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry, "base_importacao");
        $this->foundationEntityManager = $managerRegistry->getManager($connection);
        $this->estagioRepository       = self::getEntityManager()->getRepository(Estagio::class);
        $this->cursoRepository         = self::getEntityManager()->getRepository(Curso::class);
        $this->itemRepository          = self::getEntityManager()->getRepository(Item::class);
    }

    /**
     * Cria vários registros na base de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $franqueada_id ID da franqueada
     * @param \App\Helper\XlsxHelper $xlsxHelper Helper do XLSX
     */
    public function criar(&$mensagemErro, $franqueada_id, &$xlsxHelper)
    {
        if (FranqueadaBO::franqueadaExisteBanco($this->foundationEntityManager->getRepository(Franqueada::class), ["id" => $franqueada_id]) === true) {
            EstagioBO::excluirRegistrosPorFranqueada($this->estagioRepository, self::getEntityManager(), $franqueada_id);
            EstagioBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->cursoRepository, $this->itemRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
