<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\FollowupComercialBO;
use App\Repository\Importacao\FollowUpRepository;

/**
 *
 * @author Luiz A Costa
 */
class FollowupComercialFacade extends GenericFacade
{

    /**
     *
     * @var \App\BO\Principal\FollowupComercialBO
     */
    private $followupComercialBO;

    /**
     *
     * @var \App\Repository\Principal\InteressadoRepository
     */
    private $interessadoRepository;

    /**
     *
     * @var \App\Repository\Principal\FormularioFollowUpRepository
     */
    private $formularioFollowupRepository;

    /**
     * @var \App\Repository\Principal\FollowupComercialRepository
     */
    private $followupComercialRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->followupComercialBO          = new FollowupComercialBO(self::getEntityManager());
        $this->interessadoRepository        = self::getEntityManager()->getRepository(\App\Entity\Principal\Interessado::class);
        $this->formularioFollowupRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FormularioFollowUp::class);
        $this->followupComercialRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FollowupComercial::class);
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
        $retornoRepositorio = $this->interessadoRepository->filtraFollowupInteressado($parametros);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];

        return $retorno;
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
     * Busca descricao GrauInteresse
     *
     * @param string $siglaGrauInteresse
     *
     * @return string
     */
    private function buscaGrauInteresse($siglaGrauInteresse)
    {
        $grauInteresse = "Não cadastrado";
        switch ($siglaGrauInteresse) {
            case 'L':
            $grauInteresse = "Lead";
                break;
            case 'I':
            $grauInteresse = "Interessado";
                break;
            case 'H':
            $grauInteresse = "Hotlist";
                break;
        }

        return $grauInteresse;
    }


    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param boolean $bFollowUpInicial Indica se o follow up deve ir com a mensagem inicial
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $parametros=[], $bFollowUpInicial=false)
    {
        $nomeUsuario         = "Usuário não encontrado[" . $parametros[ConstanteParametros::CHAVE_USUARIO] . "]";
        $dataFollowUpInicial = new \DateTime();
        $followUp            = "";
        $followUpHeader      = "";

        $nomeTipoContato        = "";
        $nomeFormularioFollowup = "";
        $objetoORM = null;

        $formularioFollowupORM = null;
        if (is_null($parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP]) === false) {
            $formularioFollowupORM = $this->formularioFollowupRepository->find($parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP]);
        }

        if ($this->followupComercialBO->podeCriar($parametros, $mensagemErro) === true) {
            if (is_null($formularioFollowupORM) === false) {
                $nomeUsuario            = $parametros[ConstanteParametros::CHAVE_USUARIO]->getNome();
                $nomeFormularioFollowup = $formularioFollowupORM->getDescricaoFormulario();
                if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]) === false)) {
                    $nomeTipoContato = $parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]->getNome();
                } else {
                    unset($parametros[ConstanteParametros::CHAVE_TIPO_CONTATO]);
                }

                if ($bFollowUpInicial === true) {
                    $followUp = "Follow-Up Inicial\n";
                }

                $followUpHeader = $dataFollowUpInicial->format("d/m/Y H:i") . " - $nomeUsuario";
                if ($nomeTipoContato !== "") {
                    $nomeTipoContato .= " - Tipo de Contato: $nomeTipoContato";
                }

                $followUpHeader .= " - $nomeFormularioFollowup\n";

                if ((isset($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) > 0)) {
                    foreach ($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS] as $formularioData) {
                        $followUp .= $formularioData[ConstanteParametros::CHAVE_INTERESSADO_FOLLOW_UP_CAMPO_LABEL] . " ";
                        if ((isset($formularioData[ConstanteParametros::CHAVE_INTERESSADO_FOLLOW_UP_CAMPO_DADO]) === true)&&(empty($formularioData[ConstanteParametros::CHAVE_INTERESSADO_FOLLOW_UP_CAMPO_DADO]) === false)) {
                            $followUp .= $formularioData[ConstanteParametros::CHAVE_INTERESSADO_FOLLOW_UP_CAMPO_DADO] . "\n";
                        } else {
                            $followUp .= "\n";
                        }
                    }

                    $parametros[ConstanteParametros::CHAVE_FOLLOW_UP] = $followUpHeader . $followUp;
                }
            }//end if

            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\FollowupComercial::class, true, $parametros);
            self::criarRegistro($objetoORM, $mensagemErro);
        }//end if

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
        return empty($mensagemErro);
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
     * Busca os dados para o relatório de FollowUpComercial
     * 
     * 
     */
    public function buscarDadosRelatorioFollowupComercial($filtros)
    {
        return $this->interessadoRepository->gerarDadosRelatorioFollowupComercial($filtros);
    }

}
