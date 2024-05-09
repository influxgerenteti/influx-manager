<?php

namespace App\Facade\Importacao;


use App\Entity\Importacao\Turma;
use App\Entity\Principal\Franqueada;
use App\Facade\Principal\GenericFacade;
use App\BO\Importacao\TurmaBO;
use App\BO\Principal\FranqueadaBO;
use Doctrine\Common\Persistence\ManagerRegistry;
/**
 *
 * @author Luiz A Costa
 */
class TurmaFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\TurmaRepository
     */
    private $turmaRepository;

    /**
     *
     * @var \App\Repository\Importacao\CursoRepository
     */
    private $cursoRepository;

    /**
     *
     * @var \App\Repository\Importacao\SalaRepository
     */
    private $salaRepository;

    /**
     *
     * @var \App\Repository\Importacao\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     *
     * @var \App\Repository\Importacao\EstagioRepository
     */
    private $estagioRepository;

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
        $this->turmaRepository         = self::getEntityManager()->getRepository(Turma::class);
        $this->cursoRepository         = self::getEntityManager()->getRepository(\App\Entity\Importacao\Curso::class);
        $this->estagioRepository       = self::getEntityManager()->getRepository(\App\Entity\Importacao\Estagio::class);
        $this->salaRepository          = self::getEntityManager()->getRepository(\App\Entity\Importacao\Sala::class);
        $this->funcionarioRepository   = self::getEntityManager()->getRepository(\App\Entity\Importacao\Funcionario::class);
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
            TurmaBO::excluirRegistrosPorFranqueada($this->turmaRepository, self::getEntityManager(), $franqueada_id);
            TurmaBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->salaRepository, $this->cursoRepository, $this->estagioRepository, $this->funcionarioRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
