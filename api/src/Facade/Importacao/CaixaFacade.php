<?php

namespace App\Facade\Importacao;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Importacao\Caixa;
use App\Entity\Importacao\Empresa;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\BO\Importacao\CaixaBO;

/**
 *
 * @author Luiz A Costa
 */
class CaixaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Importacao\EmpresaRepository
     */
    private $empresaRepository;

    /**
     *
     * @var \App\Repository\Importacao\CaixaRepository
     */
    private $caixaRepository;

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
        $this->caixaRepository         = self::getEntityManager()->getRepository(Caixa::class);
        $this->empresaRepository       = self::getEntityManager()->getRepository(Empresa::class);
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
            CaixaBO::excluirRegistrosPorFranqueada($this->caixaRepository, self::getEntityManager(), $franqueada_id);
            CaixaBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->empresaRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
