<?php

namespace App\Facade\Importacao;


use App\BO\Principal\FranqueadaBO;
use App\BO\Importacao\ItemBO;
use App\Entity\Principal\Franqueada;
use App\Facade\Principal\GenericFacade;
use App\Entity\Importacao\Item;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class ItemFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\ItemRepository
     */
    private $itemRepository;

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
            ItemBO::excluirRegistrosPorFranqueada($this->itemRepository, self::getEntityManager(), $franqueada_id);
            ItemBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
