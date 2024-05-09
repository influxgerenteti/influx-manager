<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Marcelo Andre Naegeler
 */
class ModuloBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "acaoSistemaRepository" => $entityManager->getRepository(\App\Entity\Principal\AcaoSistema::class),
                "moduloRepository"      => $entityManager->getRepository(\App\Entity\Principal\Modulo::class),
            ]
        );
    }

    /**
     * Verifica se o array de AcaoSistema é valido ou não
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    private function verificaAcaoSistemaValidos(&$parametros, &$mensagemErro)
    {
        $bRetorno        = true;
        $acaoSistemaORMs = [];
        if ((isset($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS]) === true) && (count($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS]) > 0)) {
            foreach ($parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS] as $acaoSistemaId) {
                $acaoSistemaORM = null;
                if (self::verificaAcaoSistemaExisteBO([ConstanteParametros::CHAVE_ACAO_SISTEMA => $acaoSistemaId], $mensagemErro, $acaoSistemaORM) === false) {
                    $mensagemErro = "AcaoSistema com a ID: " . $acaoSistemaId . ", não encontrado!\n" . $mensagemErro;
                    $bRetorno     = false;
                    break;
                } else {
                    $acaoSistemaORMs[] = $acaoSistemaORM;
                }
            }

            $parametros[ConstanteParametros::CHAVE_ACAO_SISTEMAS] = $acaoSistemaORMs;
        }

        return $bRetorno;
    }

    /**
     * Verifica se o modulo pai existe na base
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    private function verificaModuloPaiValido(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_MODULO_PAI]) === true) && (empty($parametros[ConstanteParametros::CHAVE_MODULO_PAI]) === false)) {
            $bRetorno = self::verificaModuloExisteBO([ConstanteParametros::CHAVE_MODULO => $parametros[ConstanteParametros::CHAVE_MODULO_PAI]], $mensagemErro, $parametros[ConstanteParametros::CHAVE_MODULO_PAI]);
        }

        return $bRetorno;
    }

    /**
     * Verifica os parametros relacionais opcionais
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoAcaoSistema = $this->verificaAcaoSistemaValidos($parametros, $mensagemErro);
        $bRetornoModuloPai   = $this->verificaModuloPaiValido($parametros, $mensagemErro);

        return ($bRetornoAcaoSistema && $bRetornoModuloPai);
    }

    /**
     * Verifica se as regras aplicadas são validas
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Busca filhos no array da lista
     *
     * @param array $pai array que contem o indice filhos
     * @param array $lista array que contem lista original
     *
     * @return array
     */
    public static function buscaFilhos($pai, $lista)
    {
        $pai['filhos'] = array_values(
            array_filter(
                $lista,
                function ($item) use ($pai) {
                    return $item['modulo_pai_id'] === $pai['id'];
                }
            )
        );
        $pai['filhos'] = array_map(
            function ($item) use ($lista) {
                return self::buscaFilhos($item, $lista);
            },
            $pai['filhos']
        );
        return $pai;
    }

     /**
      * Monta um arvore de menu
      *
      * @param array $lista array que contem lista original
      *
      * @return array
      */
    public static function montaArvore($lista)
    {
        $itens = [];

        $arvore = array_filter(
            $lista,
            function ($item) {
                return empty($item['modulo_pai_id']) === true;
            }
        );
        $itens  = array_map(
            function ($pai) use ($lista) {
                $listaFilhos = array_filter(
                    $lista,
                    function ($item2) {
                        return empty($item2['modulo_pai_id']) === false;
                    }
                );
                return self::buscaFilhos($pai, $listaFilhos);
            },
            $arvore
        );
        return $itens;
    }

    /**
     * Função para estruturação do modulo lateral com os favoritos do usuário.
     *
     * @param array $itens Itens do modulo a serem organizados
     *
     * @return array
     */
    public static function organizarItens ($itens=[])
    {
        $modulosOrganizados = [];
        $favoritos          = [];
        $totalItens         = count($itens);

        for ($index = 0; $index < $totalItens; $index++) {
            $item = $itens[$index];
            if (empty($item['favorito_id']) === false) {
                $favoritos[] = $item;
            }
        }

        $modulosOrganizados = self::montaArvore($itens);
        $modulosOrganizados = array_values($modulosOrganizados);
        return [
            'favoritos' => $favoritos,
            'modulos'   => $modulosOrganizados,
        ];
    }

    /**
     * Verifica se o Modulo existe atraves do campo ID
     *
     * @param \App\Repository\Principal\ModuloRepository $moduloRepository Repositorio da Modulo
     * @param int $id Chave primaria a ser consultada
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Modulo $moduloORM Ponteiro para retornar o objeto
     *
     * @return boolean
     */
    public static function verificaModuloExiste(\App\Repository\Principal\ModuloRepository $moduloRepository, $id, &$mensagemErro, &$moduloORM=null)
    {
        $moduloORM = $moduloRepository->find($id);
        if (is_null($moduloORM) === true) {
            $mensagemErro = "Modulo não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
