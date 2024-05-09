<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Principal\Estado;
use App\Entity\Principal\Cidade;
use App\Entity\Principal\Cep;

/**
 *
 * @author Luiz Antonio Costa
 */
class CepBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\CepRepository
     */
    private $cepRepository;

    /**
     *
     * @var \App\Repository\Principal\EstadoRepository
     */
    private $estadoRepository;

    /**
     *
     * @var \App\Repository\Principal\CidadeRepository
     */
    private $cidadeRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->cepRepository    = $entityManager->getRepository(\App\Entity\Principal\Cep::class);
        $this->estadoRepository = $entityManager->getRepository(\App\Entity\Principal\Estado::class);
        $this->cidadeRepository = $entityManager->getRepository(\App\Entity\Principal\Cidade::class);
    }

    /**
     * Cria um CEP para o estado e cidade informado
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager EntityManager para cadastrar um novo CEP
     * @param \stdClass $dadosCurl Dados JSON formatados
     * @param \App\Entity\Principal\Estado $estadoORM
     * @param \App\Entity\Principal\Cidade $cidadeORM
     *
     * @return \App\Entity\Principal\Cep|NULL
     */
    protected function criarCep($entityManager, $dadosCurl, $estadoORM, $cidadeORM)
    {
        $cepORM = new Cep();
        $cepORM->setEstado($estadoORM);
        $cepORM->setCidade($cidadeORM);
        $cepORM->setCep(str_replace("-", "", $dadosCurl->cep));
        $cepORM->setBairro($dadosCurl->bairro);
        $cepORM->setRua($dadosCurl->logradouro);
        $entityManager->persist($cepORM);
        $entityManager->flush();

        return $cepORM;
    }

    /**
     * Busca uma cidade com base no retorno da API, caso não exista, ele irá criar
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager EntityManager para cadastrar um novo CEP
     * @param \stdClass $dadosCurl Dados JSON formatados
     * @param \App\Entity\Principal\Estado $estadoORM
     *
     * @return \App\Entity\Principal\Cidade|NULL
     */
    protected function criarCidadeApi($entityManager, $dadosCurl, $estadoORM)
    {
        $cidadeORM = $this->cidadeRepository->findOneBy(["nome" => $dadosCurl->localidade]);
        if (is_null($cidadeORM) === true) {
            $cidadeORM = new Cidade();
            $cidadeORM->setEstado($estadoORM);
            $cidadeORM->setNome($dadosCurl->localidade);
            $entityManager->persist($cidadeORM);
        }

        return $cidadeORM;
    }

    /**
     * Busca o estado atraves da Sigla, caso não exista na base, cria um registro novo no banco
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager EntityManager para cadastrar um novo CEP
     * @param \stdClass $dadosCurl Dados JSON formatados
     *
     * @return \App\Entity\Principal\Estado|NULL
     */
    protected function criarEstadoApi($entityManager, $dadosCurl)
    {
        $estadoORM = $this->estadoRepository->findOneBy([ "sigla" => $dadosCurl->uf]);
        if (is_null($estadoORM) === true) {
            $estadoORM = new Estado();
            $estadoORM->setNome("AUSENCIA ESTADO! Solicitar Equipe para preencher este campo.");
            $estadoORM->setSigla($dadosCurl->uf);
            $entityManager->persist($estadoORM);
        }

        return $estadoORM;
    }

    /**
     * Cria um CEP na base de dados
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager EntityManager para cadastrar um novo CEP
     * @param \stdClass $dadosCurl Dados JSON formatados
     *
     * @return \App\Entity\Principal\Cep|NULL
     */
    public function importarCepDB($entityManager, $dadosCurl)
    {
        $estadoORM = $this->criarEstadoApi($entityManager, $dadosCurl);
        $cidadeORM = $this->criarCidadeApi($entityManager, $dadosCurl, $estadoORM);
        return $this->criarCep($entityManager, $dadosCurl, $estadoORM, $cidadeORM);
    }

    /**
     * Verifica se existe o estado no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\EstadoRepository $estadoRepositoy Repositorio do estado
     * @param integer $id Chave primaria do estado
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Estado|null $estadoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaEstadoExiste(\App\Repository\Principal\EstadoRepository $estadoRepositoy, $id, &$mensagemErro, &$estadoORM)
    {
        $estadoORM = $estadoRepositoy->find($id);
        if ($estadoORM === null) {
            $mensagemErro = "Estado não encontrado na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe a cidade no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\CidadeRepository $cidadeRepository Repositorio da cidade
     * @param integer $id Chave primaria da cidade
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\Cidade|null $cidadeORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaCidadeExiste(\App\Repository\Principal\CidadeRepository $cidadeRepository, $id, &$mensagemErro, &$cidadeORM)
    {
        $cidadeORM = $cidadeRepository->find($id);
        if ($cidadeORM === null) {
            $mensagemErro = "Cidade não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
