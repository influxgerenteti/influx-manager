<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\InteressadoBO;

/**
 *
 * @author Luiz A Costa
 */
class InteressadoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\InteressadoRepository
     */
    private $interessadoRepository;

    /**
     *
     * @var \App\BO\Principal\InteressadoBO
     */
    private $interessadoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->interessadoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Interessado::class);
        $this->interessadoBO         = new InteressadoBO(self::getEntityManager());
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
        $retornoRepositorio = $this->interessadoRepository->filtraInteressadoPorPagina($parametros);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca a lista de interessados para o Funil de Vendas
     *
     * @param int $usuarioId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function listaFunilVendas($usuarioId, $parametros=[])
    {
        return $this->interessadoRepository->buscaFunilVendas($usuarioId, $parametros);
    }

    /**
     * Busca a lista de interessados para o Funil de Vendas
     *
     * @param int $usuarioId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function listaFunilVendasAtrasado($usuarioId, $parametros=[])
    {
        return $this->interessadoRepository->buscaFunilVendasAtrasado($usuarioId, $parametros);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $retorno = $this->interessadoRepository->buscaPorId($id);
        if (is_null($retorno) === true) {
            $mensagemErro = "Registro não encontrado na base de dados.";
        }

        return $retorno;
    }

    /**
     * Busca um registro atraves do nome
     *
     * @param string $query nome a ser buscado
     *
     * @return NULL|\App\Entity\Principal\Interessado
     */
    public function buscarPorNome ($query)
    {
        return $this->interessadoRepository->buscaInteressadoPorNome($query);
    }

    /**
     * Busca um registro atraves do nome ou telefone
     *
     * @param string $query nome a ser buscado
     *
     * @return NULL|\App\Entity\Principal\Interessado
     */
    public function buscarPorNomeOuTelefone ($query)
    {
        return $this->interessadoRepository->buscaInteressadoPorNomeOuTelefone($query);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Interessado
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->interessadoBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Interessado::class);
            $this->interessadoBO->configuraCampos($parametros, $objetoORM, $mensagemErro);
            if (empty($mensagemErro) === true) {
                self::criarRegistro($objetoORM, $mensagemErro);
            }
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
    public function atualizar(&$mensagemErro, $id, &$parametros=[])
    {
       // echo date("H:i:s").": "."p1".PHP_EOL;
     
        $objetoORM = $this->interessadoRepository->find($id);

       // echo date("H:i:s").": "."p2".PHP_EOL;
        
        $parametros[ConstanteParametros::CHAVE_INTERESSADO] = $objetoORM;

       // echo date("H:i:s").": "."p3".PHP_EOL;
        
        if (is_null($objetoORM) === true) {            
            $mensagemErro = "Interessado não encontrado na base de dados.";
        } else {
            if ((int) $parametros[ConstanteParametros::CHAVE_FRANQUEADA] !== $objetoORM->getFranqueada()->getId()) {
                $mensagemErro = "Interessado não pertence a franqueada selecionada.";
            } else {
                unset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
             //   echo date("H:i:s").": "."p4".PHP_EOL;
                if ($this->interessadoBO->podeAlterar($parametros, $mensagemErro) === true) {
                    
                    if ($parametros[ConstanteParametros::CHAVE_CURSO] || 
                            $parametros[ConstanteParametros::CHAVE_IDIOMAS] ||
                            $parametros[ConstanteParametros::CHAVE_WORKFLOW_ACAO] ||
                            $parametros[ConstanteParametros::CHAVE_TIPO_LEAD]) {
                            // echo date("H:i:s").": "."p5".PHP_EOL;
                             $idiomas      = $objetoORM->getIdiomas();
                            // echo date("H:i:s").": "."p6".PHP_EOL;
                             $totalIdiomas = $idiomas->count();
                             for ($i = 0; $i < $totalIdiomas;$i++) {
                                 $objetoORM->removeIdioma($idiomas->get($i));
                             }
                             // echo date("H:i:s").": "."p7".PHP_EOL;
                             $this->interessadoBO->configuraCampos($parametros, $objetoORM, $mensagemErro);
                             // echo date("H:i:s").": "."p8".PHP_EOL;
                            } else {
                               if ($parametros[ConstanteParametros::CHAVE_NOME]) {
                                    $objetoORM->setNome($parametros[ConstanteParametros::CHAVE_NOME]);
                                }
                                if ($parametros[ConstanteParametros::CHAVE_IDADE]) {
                                    $objetoORM->setIdade($parametros[ConstanteParametros::CHAVE_IDADE]);                                    
                                }
                                if ($parametros['email_contato']) {
                                    $objetoORM->setEmailContato($parametros['email_contato']);                                    
                                }
                                if ($parametros[ConstanteParametros::CHAVE_TELEFONE_CONTATO]) {
                                    $objetoORM->setTelefoneContato($parametros[ConstanteParametros::CHAVE_TELEFONE_CONTATO]);
                                }
                                if ($parametros['sexo']) {
                                    $objetoORM->setSexo($parametros['sexo']);
                                }
                                if ($parametros['telefone_secundario']) {
                                    $objetoORM->setTelefoneSecundario($parametros['telefone_secundario']);
                                }
                                if ($parametros['email_secundario']) {
                                    $objetoORM->setEmailSecundario($parametros['email_secundario']);
                                }
                            }

                    if (empty($mensagemErro) === true) {
                        self::flushSeguro($mensagemErro);
                    }
                }//end if
            }//end if
        }//end if
       // echo date("H:i:s").": "."p9".PHP_EOL;
        //  die;
        return true;
    }

    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param array  $parametros
     *
     * @return string
     */
    public function gerarDadosRelatorio($parametros, $relatorio)
    {
        if ($relatorio === '1') {
            return $this->interessadoRepository->prepararDadosRelatorioInteressadosPeriodo($parametros);
        } else {
            return $this->interessadoRepository->prepararDadosRelatorioMatriculasPerdidas($parametros);
        }
    }

    /**
     * Busca se um dado telefone está cadastrado como o de algum interessado
     *
     * @param string $mensagemErro Mensagem de erro a ser retornada para o front-end quando ocorrer um erro
     * @param array $parametros
     *
     * @return boolean
     */
    public function buscarTelefoneEstaCadastrado(&$mensagemErro, $parametros)
    {
        $interessados = $this->interessadoRepository->buscarPorTelefone($parametros);

        return !is_null($interessados);
    }

    public function gerarDadosRelatorioRetornoConsultor($filtros) {
        return $this->interessadoRepository->consultaDadosRelatorioRetornoConsultor($filtros);
    }

    public function buscarDadosRelatorioContato($filtros)
    {
        return $this->interessadoRepository->gerarDadosRelatorioContatos($filtros);
    }

    public function buscarDadosRelatorioConsultaDesistencias($filtros)
    {
        return $this->interessadoRepository->gerarDadosRelatorioConsultaDesistencias($filtros);
    }

    public function buscarDadosRelatorioConsultaConversao($filtros)
    {
        return $this->interessadoRepository->gerarDadosRelatorioConsultaConversao($filtros);
    }

    public function buscarDadosRelatorioProspeccao($filtros){
        return $this->interessadoRepository->gerarDadosRelatorioProspeccao($filtros);
    }
}
