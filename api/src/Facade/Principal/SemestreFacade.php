<?php

namespace App\Facade\Principal;

use App\Entity\Principal\Semestre;
use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use DateTime;

/**
 *
 * @author Luiz A Costa
 */
class SemestreFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\SemestreRepository
     */
    private $semestreRepository;

    /**
     * Configura as datas de inicio e termino do semestre
     *
     * @param array $parametros
     * @param string $mensagemErro
     */
    protected function configuraDataInicioDataTermino(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_INICIO], $parametros[ConstanteParametros::CHAVE_DATA_INICIO]);
            if ($parametros[ConstanteParametros::CHAVE_DATA_INICIO] === false) {
                $mensagemErro .= "Formato de data invalido.";
                $bRetorno      = false;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_TERMINO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_TERMINO]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_TERMINO], $parametros[ConstanteParametros::CHAVE_DATA_TERMINO]);
            if ($parametros[ConstanteParametros::CHAVE_DATA_TERMINO] === false) {
                $mensagemErro .= "Formato de data invalido.";
                $bRetorno      = false;
            }
        }

        return $bRetorno;
    }

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->semestreRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Semestre::class);
    }

    /**
     * Buscar ou criar semestre com base nos dados da base
     *
     * @param DateTime $data
     */
    private function buscarOuCriarProximoSemestre($data)
    {

        $objetoORM = $this->semestreRepository->buscarProximoSemestre($data);
        if (is_object($objetoORM) === false) {
            // Caso o não retorne proximo semestre criar um novo
            $ano      = ((int) $data->format("Y"));
            $mes      = ((int) $data->format("m"));
            $semestre = "";
            if ($mes <= 6) {
                $semestre = "01";
            } else {
                $semestre = "02";
            }

            $dataInicial = \DateTime::createFromFormat('Y-m-d', "$ano-01-20");
            $dataTermino = \DateTime::createFromFormat('Y-m-d', "$ano-06-25");

            if ($semestre === "02") {
                $dataInicial = \DateTime::createFromFormat('Y-m-d', "$ano-07-15");
                $dataTermino = \DateTime::createFromFormat('Y-m-d', "$ano-12-18");
            }

            $objetoORM = new Semestre();
            $objetoORM->setDescricao("$ano/$semestre");
            $objetoORM->setDataInicio($dataInicial);
            $objetoORM->setDataTermino($dataTermino);

            $mensagemErro = '';
            self::criarRegistro($objetoORM, $mensagemErro);
        }//end if

        return $objetoORM;
    }

    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     * @param string $mensagemErro Mensagem de erro
     *
     * @return array
     */
    public function listar($parametros, &$mensagemErro)
    {
        $listaDeRetorno     = [];
        $retornoRepositorio = $this->semestreRepository->filtrarSemestrePorPagina($parametros);
        if ((isset($parametros[ConstanteParametros::CHAVE_ANTERIOR_ATUAL_PROXIMO]) === true) && ((bool) $parametros[ConstanteParametros::CHAVE_ANTERIOR_ATUAL_PROXIMO]) === true) {
            $semestreAtualORM = $this->semestreRepository->buscarSemestreAtual();
            if (is_object($semestreAtualORM) === true) {
                $data = $semestreAtualORM->getDataInicio();
                $semestreProximoORM = $this->buscarOuCriarProximoSemestre($data);

                $data      = $semestreAtualORM->getDescricao();
                $descricao = "";

                list($ano, $semestre) = explode('/', $data);
                $ano      = (int) $ano;
                $semestre = (int) $semestre;

                if ($semestre === 1) {
                    $ano       = $ano - 1;
                    $descricao = "$ano/02";
                } else if ($semestre === 2) {
                    $descricao = "$ano/01";
                }

                $semestreAnteriorORM = $this->semestreRepository->buscarSemestrePorDescricao($descricao);
            } else {
                $mensagemErro = "Semestre atual não existe na base";
            }//end if

            if (is_null($semestreAtualORM) === false) {
                $listaDescricaoSemetres = [
                    $semestreAtualORM->getDescricao(),
                    $semestreAnteriorORM->getDescricao(),
                    $semestreProximoORM->getDescricao(),
                ];

                foreach ($retornoRepositorio->getItems() as $item) {
                    if (in_array($item[ConstanteParametros::CHAVE_DESCRICAO], $listaDescricaoSemetres) === true) {
                        array_push($listaDeRetorno, $item);
                    }
                }
            }

            $listaDeRetorno = $listaDeRetorno;
        } else {
            $listaDeRetorno = $retornoRepositorio->getItems();
        }//end if

        $retorno = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $listaDeRetorno,
        ];

        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|NULL
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->semestreRepository->find($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Semestre não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Semestre
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->configuraDataInicioDataTermino($parametros, $mensagemErro) === true) {
            if (\App\BO\Principal\GenericBO::verificaDataFinalMaiorDataInicio($parametros[ConstanteParametros::CHAVE_DATA_TERMINO], $parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === true) {
                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Semestre::class, true, $parametros);
                self::criarRegistro($objetoORM, $mensagemErro);
            }
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->semestreRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Semestre não encontrada na base de dados.";
        } else {
            if ($this->configuraDataInicioDataTermino($parametros, $mensagemErro) === true) {
                if (\App\BO\Principal\GenericBO::verificaDataFinalMaiorDataInicio($parametros[ConstanteParametros::CHAVE_DATA_TERMINO], $parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === true) {
                    \App\Helper\FunctionHelper::setParams($parametros, $objetoORM);
                    self::flushSeguro($mensagemErro);
                }
            }
        }

        return empty($mensagemErro);
    }


}
