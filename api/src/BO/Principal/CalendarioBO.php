<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Luiz Antonio Costa
 */
class CalendarioBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\CalendarioRepository
     */
    private $calendarioRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->calendarioRepository = $entityManager->getRepository(\App\Entity\Principal\Calendario::class);
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
            ]
        );
    }

    /**
     * Verifica os campos relacionais obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\CalendarioRepository $calendarioRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Calendario $calendarioORM
     *
     * @return boolean
     */
    public static function verificaCalendarioExiste(\App\Repository\Principal\CalendarioRepository $calendarioRepository, $id, &$mensagemErro, &$calendarioORM)
    {
        $calendarioORM = $calendarioRepository->find($id);
        if (is_null($calendarioORM) === true) {
            $mensagemErro = "Calendário não encontrado.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se os campos relacionais estao validos e indica se pode salvar ou não
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\Calendario $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) === false)) {
            $data = $parametros[ConstanteParametros::CHAVE_DATA_INICIAL];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $data);
            $objetoORM->setDataInicial($data);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_FINAL]) === false)) {
            $data = $parametros[ConstanteParametros::CHAVE_DATA_FINAL];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $data);
            $objetoORM->setDataFinal($data);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_FERIADO_BANCARIO]) === true) {
            $objetoORM->setFeriadoBancario($parametros[ConstanteParametros::CHAVE_FERIADO_BANCARIO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DIA_LETIVO]) === true) {
            $objetoORM->setDiaLetivo($parametros[ConstanteParametros::CHAVE_DIA_LETIVO]);
        }
    }

    /**
     * Aplica os feriados nos anos que possuam 2000
     *
     * @param int $ano
     * @param \App\Entity\Principal\Calendario[] $arrayCalendario
     *
     * @return array
     */
    protected function aplicaFeriadoDatas($ano, $arrayCalendario)
    {
        $arrayItems = [];
        foreach ($arrayCalendario as $calendarioObjeto) {
            $dataDB = $calendarioObjeto->getDataInicial();
            if ($calendarioObjeto->getFeriadoAnual() === true) {
                $dataAtual = new \DateTime();
                $dataAtual->setDate($ano, intval($dataDB->format('m')), intval($dataDB->format('d')));
                $calendarioObjeto->setDataInicial($dataAtual);
            }

            $arrayItems[] = $calendarioObjeto;
        }

        return $arrayItems;
    }

    /**
     * Monta a estrutura de dados para o front-end
     *
     * @param \App\Entity\Principal\Calendario[] $listaDatas
     *
     * @return array
     */
    protected function montaArrayFrontEnd($listaDatas)
    {
        $retornoFrontEnd = [];
        foreach ($listaDatas as $calendarioORM) {
            $dataInicial       = $calendarioORM->getDataInicial();
            $dataFinal         = $calendarioORM->getDataFinal();
            $metaDataFrontEnd  = [
                ConstanteParametros::CHAVE_ID               => $calendarioORM->getId(),
                ConstanteParametros::CHAVE_MES              => intval($dataInicial->format("m")),
                ConstanteParametros::CHAVE_DATA_INICIAL     => $dataInicial,
                ConstanteParametros::CHAVE_DATA_FINAL       => $dataFinal,
                ConstanteParametros::CHAVE_DESCRICAO        => $calendarioORM->getDescricao(),
                ConstanteParametros::CHAVE_FERIADO_BANCARIO => $calendarioORM->getFeriadoBancario(),
                ConstanteParametros::CHAVE_FERIADO_ANUAL    => $calendarioORM->getFeriadoAnual(),
                ConstanteParametros::CHAVE_DIA_LETIVO       => $calendarioORM->getDiaLetivo(),
                "permite_edicao"                            => $calendarioORM->getFranqueada()->getId() === intval(VariaveisCompartilhadas::$franqueadaID),
            ];
            $retornoFrontEnd[] = $metaDataFrontEnd;
            if ((is_null($dataFinal) === false) && (intval($dataInicial->format("m"))) !== intval($dataFinal->format("m"))) {
                $metaDataFrontEnd[ConstanteParametros::CHAVE_MES] = intval($dataFinal->format("m"));
                $retornoFrontEnd[] = $metaDataFrontEnd;
            }
        }

        return $retornoFrontEnd;
    }

    /**
     * Retorna uma lista de datas formatadas para o front-end
     *
     * @param int $anoParaFeriados
     * @param \App\Entity\Principal\Calendario[] $listaDatas
     *
     * @return array
     */
    public function retornaListaCalendarioCustomizado($anoParaFeriados, $listaDatas)
    {
        $dadosComFeriadosAplicados = $this->aplicaFeriadoDatas($anoParaFeriados, $listaDatas);
        return $this->montaArrayFrontEnd($dadosComFeriadosAplicados);
    }

    /**
     * Verifica se o calendário existe e está ativo
     *
     * @param \App\Repository\Principal\CalendarioRepository $repository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Calendario $calendario
     *
     * @return boolean
     */
    public static function calendarioExisteEAtivo (\App\Repository\Principal\CalendarioRepository $repository, $id, &$mensagemErro, &$calendario)
    {
        if (self::verificaCalendarioExiste($repository, $id, $mensagemErro, $calendario) === false) {
            return false;
        }

        if ($calendario->getSituacao() !== 'A') {
            $mensagemErro = 'O calendário selecionado está inativo.';
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe um registro de dia não letivo segundo a data passada, franqueada e calendário
     *
     * @param \App\Entity\Principal\Franqueada $franqueada
     * @param \DateTime $data
     *
     * @return boolean
     */
    public function existeDiaNaoLetivoNoCalendario ($franqueada, $data)
    {
        return empty($this->calendarioRepository->buscaDataNaoLetiva($franqueada, $data)) === false;
    }

    /**
     * Verifica se  a data passada é feriado bancário
     *
     * @param \App\Entity\Principal\Franqueada $franqueada
     * @param \DateTime $data
     *
     * @return boolean
     */
    public function verificaFeriadoBancarioPorData ($franqueada, $data)
    {
        return empty($this->calendarioRepository->buscaFeriadoBancario($franqueada, $data)) === false;
    }


}
