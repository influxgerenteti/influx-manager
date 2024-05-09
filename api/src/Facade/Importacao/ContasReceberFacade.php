<?php

namespace App\Facade\Importacao;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Importacao\ContasReceber;
use App\Entity\Importacao\Aluno;
use App\Entity\Importacao\Empresa;
use App\BO\Principal\FranqueadaBO;
use App\BO\Importacao\ContasReceberBO;
use App\Entity\Principal\Franqueada;

/**
 *
 * @author Luiz A Costa
 */
class ContasReceberFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Importacao\ContasReceberRepository
     */
    private $contasReceberRepository;

    /**
     *
     * @var \App\Repository\Importacao\EmpresaRepository
     */
    private $empresaRepository;

    /**
     *
     * @var \App\Repository\Importacao\AlunoRepository
     */
    private $alunoRepository;

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
        $this->contasReceberRepository = self::getEntityManager()->getRepository(ContasReceber::class);
        $this->empresaRepository       = self::getEntityManager()->getRepository(Empresa::class);
        $this->alunoRepository         = self::getEntityManager()->getRepository(Aluno::class);
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
            ContasReceberBO::excluirRegistrosPorFranqueada($this->contasReceberRepository, self::getEntityManager(), $franqueada_id);
            ContasReceberBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->empresaRepository, $this->alunoRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
