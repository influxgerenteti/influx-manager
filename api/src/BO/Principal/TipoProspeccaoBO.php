<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class TipoProspeccaoBO
{

    /**
     *
     * @var \App\Repository\Principal\TipoProspeccaoRepository
     */
    private $tipoProspeccaoRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->tipoProspeccaoRepository = $entityManager->getRepository(\App\Entity\Principal\TipoProspeccao::class);
    }

    /**
     * Configuracao do tipo de prospeccao
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\TipoProspeccao $objetoORM
     */
    public function configuraParametros($parametros, &$mensagemErro, &$objetoORM)
    {
        if ($parametros[ConstanteParametros::CHAVE_TIPO_PAI_TIPO_PROSPECCAO] === null) {
            $objetoORM->setTipoPaiTipoProspeccao(null);
        } else {
            if ($this->verificaTipoProspeccaoExiste($this->tipoProspeccaoRepository, $parametros[ConstanteParametros::CHAVE_TIPO_PAI_TIPO_PROSPECCAO], $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_PAI_TIPO_PROSPECCAO]) === true) {
                $objetoORM->setTipoPaiTipoProspeccao($parametros[ConstanteParametros::CHAVE_TIPO_PAI_TIPO_PROSPECCAO]);
            } else {
                $mensagemErro .= "TipoPaiTipoProspeccao não encontrada.\n";
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
            $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
        }
    }

    /**
     * Verifica se o tipo prospeccao existe na base de dados
     *
     * @param \App\Repository\Principal\TipoProspeccaoRepository $tipoProspeccaoRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\TipoProspeccao $retornoORM
     *
     * @return boolean
     */
    public static function verificaTipoProspeccaoExiste(\App\Repository\Principal\TipoProspeccaoRepository $tipoProspeccaoRepository, $id, &$mensagemErro, &$retornoORM=null)
    {
        $retornoORM = $tipoProspeccaoRepository->find($id);
        if (is_null($retornoORM) === true) {
            $mensagemErro = "TipoProspeccao não encontrado na base de dados\n";
            return false;
        }

        return true;
    }


}
