<?php

namespace App\Facade\Importacao;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Importacao\ContasPagar;
use App\Entity\Importacao\Empresa;
use App\Entity\Importacao\Funcionario;
use App\BO\Importacao\ContasPagarBO;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;

/**
 *
 * @author Luiz A Costa
 */
class ContasPagarFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Importacao\ContasPagarRepository
     */
    private $contasPagarRepository;

    /**
     *
     * @var \App\Repository\Importacao\EmpresaRepository
     */
    private $empresaRepository;

    /**
     *
     * @var \App\Repository\Importacao\FuncionarioRepository
     */
    private $funcionarioRepository;

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
        $this->contasPagarRepository   = self::getEntityManager()->getRepository(ContasPagar::class);
        $this->empresaRepository       = self::getEntityManager()->getRepository(Empresa::class);
        $this->funcionarioRepository   = self::getEntityManager()->getRepository(Funcionario::class);
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
            ContasPagarBO::excluirRegistrosPorFranqueada($this->contasPagarRepository, self::getEntityManager(), $franqueada_id);
            ContasPagarBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->empresaRepository, $this->funcionarioRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
