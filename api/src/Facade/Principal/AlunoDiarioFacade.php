<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\AlunoDiarioBO;
use App\Helper\ConstanteParametros;
use App\Helper\FunctionHelper;

/**
 *
 * @author Luiz A Costa
 */
class AlunoDiarioFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AlunoDiarioRepository
     */
    private $alunoDiarioRepository;

    /**
     *
     * @var \App\BO\Principal\AlunoDiarioBO
     */
    private $alunoDiarioBO;

    /**
     *
     * @var \App\Facade\Principal\LicaoFacade
     */
    private $licaoFacade;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->licaoFacade           = new LicaoFacade($managerRegistry);
        $this->alunoDiarioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoDiario::class);
        $this->alunoDiarioBO         = new AlunoDiarioBO(self::getEntityManager());
        $this->funcionarioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Funcionario::class);
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {

    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|Object
     */
    public function buscarPorId(&$mensagemErro, $id)
    {

    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param NULL|\App\Entity\Principal\AlunoDiario $retornoORM retorno do objeto
     *
     * @return bool
     */
    public function criar(&$mensagemErro, $parametros=[], &$retornoORM=null)
    {
        $objetoORM = null;
        if ($this->alunoDiarioBO->podeCriar($parametros, $mensagemErro) === true) {
            $licaosId = $parametros[ConstanteParametros::CHAVE_LICAOS];
            unset($parametros[ConstanteParametros::CHAVE_LICAOS]);
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AlunoDiario::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
            if (empty($mensagemErro) === true) {
                foreach ($licaosId as $licaoId) {
                    $this->licaoFacade->atualizarLicaoComAlunoDiario($mensagemErro, $licaoId, $objetoORM);
                }

                $retornoORM = $objetoORM;
            }
        }

        return (is_null($objetoORM) === false) && (empty($mensagemErro) === true);
    }

    /**
     * Atualiza as frequencias do aluno
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $alunoDiario = $this->alunoDiarioRepository->find($id);
        if (is_null($alunoDiario) === true) {
            $mensagemErro = "AlunoDiario não encontrado na base de dados.";
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_PRESENCA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_PRESENCA]) === false)) {
                $alunoDiario->setPresenca($parametros[ConstanteParametros::CHAVE_PRESENCA]);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_ATIVIDADE_CA]) === true) {
                $alunoDiario->setAtividadeCa($parametros[ConstanteParametros::CHAVE_ATIVIDADE_CA]);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_ATIVIDADE_CE]) === true) {
                $alunoDiario->setAtividadeCe($parametros[ConstanteParametros::CHAVE_ATIVIDADE_CE]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_CONTRATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONTRATO]) === false)) {
                $alunoDiario->setContrato($parametros[ConstanteParametros::CHAVE_CONTRATO]);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true && is_null($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false) {
                $funcionarioORM = $this->funcionarioRepository->find($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
                if (is_null($funcionarioORM) === true) {
                    $mensagemErro = "Funcionário não encontrado na base de dados.";
                } else {
                    $alunoDiario->setFuncionario($funcionarioORM);
                }
            }

            $licaosId = $parametros[ConstanteParametros::CHAVE_LICAOS];
            unset($parametros[ConstanteParametros::CHAVE_LICAOS]);
            if (empty($mensagemErro) === true) {
                // Excluindo as lições que estavam cadastradas, mas que não vieram no array (foram excluidas na tela)
                $licoesCadastradasAluno = $alunoDiario->getLicao();
                foreach ($licoesCadastradasAluno as $licaoCadastradaORM) {
                    $licaoCadastradaId = $licaoCadastradaORM->getId();
                    $licaoKey          = false;
                    foreach ($licaosId as $k => $licaoId) {
                        if ($licaoId === $licaoCadastradaId) {
                            $licaoKey = $k;
                        }
                    }

                    if ($licaoKey === false) {
                        $this->licaoFacade->removerLicaoAlunoDiario($mensagemErro, $licaoCadastradaId, $alunoDiario);
                    }
                }

                foreach ($licaosId as $licaoId) {
                    $this->licaoFacade->atualizarLicaoComAlunoDiario($mensagemErro, $licaoId, $alunoDiario);
                }
            }//end if
        }//end if

        return empty($mensagemErro);
    }

    /**
     * Lancar/Atualizar frequencia dos alunos
     *
     * @param string $mensagemErro
     * @param array $parametros
     * @param NULL|\App\Entity\Principal\AlunoDiario $alunoDiarioORM
     *
     * @return boolean
     */
    public function lancarAtualizarFrequencias(&$mensagemErro, $parametros=[], &$alunoDiarioORM=null)
    {
        $bPossuiDiarioId = isset($parametros[ConstanteParametros::CHAVE_ID]);
        if ($bPossuiDiarioId === true) {
            $alunoDiarioId = $parametros[ConstanteParametros::CHAVE_ID];
            unset($parametros[ConstanteParametros::CHAVE_ID]);
            $bSuccesso = $this->atualizar($mensagemErro, $alunoDiarioId, $parametros);
        } else {
            $bSuccesso = $this->criar($mensagemErro, $parametros, $alunoDiarioORM);
        }

        return $bSuccesso;
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        return empty($mensagemErro);
    }




    /**
     * Buscar os dados pra gerar os dados do relatório de frequencia
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     */
    public function gerarDadosRelatorioFrequencia($parametros)
    {
        $anodataInicial = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_INICIAL]));
        $anodataFinal = strtotime(str_replace("/", "-",$parametros[ConstanteParametros::CHAVE_DATA_FINAL]));
        
        $anodataInicial = date('Y', $anodataInicial);
        $anodataFinal = date('Y', $anodataFinal);
        
        // Teste para verificar se o período está dentro do mesmo ano
        if($anodataFinal != $anodataInicial){
            throw new \Exception("O perído não está dentro do mesmo ano.", 400);
        }else{
            $retorno = $this->alunoDiarioRepository->buscarDadosRelatorioFrequencia($parametros);

            $soma = 0;
            foreach ($retorno as $item) {
                # code...
                $soma += $item['frequencia'];
            }           
            
            $retornoFinal = [
                'media_frequencia' => $soma/count($retorno),
                'dados' => $retorno
            ];
                
            return $retornoFinal;
        }
    }


}
