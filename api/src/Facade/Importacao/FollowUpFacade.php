<?php

namespace App\Facade\Importacao;


use App\Facade\Principal\GenericFacade;
use App\Entity\Importacao\FollowUp;
use App\BO\Principal\FranqueadaBO;
use App\BO\Importacao\FollowUpBO;
use App\Entity\Principal\Franqueada;
use App\Entity\Importacao\Aluno;
use App\Entity\Importacao\Empresa;
use App\Entity\Importacao\Funcionario;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class FollowUpFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\FollowUpRepository
     */
    private $followUpRepository;

    /**
     *
     * @var \App\Repository\Importacao\AlunoRepository
     */
    private $alunoRepository;

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
        $this->followUpRepository      = self::getEntityManager()->getRepository(FollowUp::class);
        $this->alunoRepository         = self::getEntityManager()->getRepository(Aluno::class);
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
            FollowUpBO::excluirRegistrosPorFranqueada($this->followUpRepository, self::getEntityManager(), $franqueada_id);
            FollowUpBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->alunoRepository, $this->empresaRepository, $this->funcionarioRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
