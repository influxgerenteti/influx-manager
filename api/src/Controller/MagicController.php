<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ManagerRegistry;

class MagicController extends AbstractController
{
    private $manager;

    function __construct(ManagerRegistry $managerRegistry)
    {
        $this->manager = $managerRegistry->getManager("base_principal");
    }

    /**
     * Retorna a entidade com base no nome de parâmetro passado.
     * Exemplo: parametro da request = 'consultor_funcionario'
     *          $paramArray = ['Consultor', 'Funcionario']
     *
     * @param array $paramArray
     *
     * @return string caminho da entidade
     */
    private function getClassName($paramArray)
    {
        if (count($paramArray) === 0) {
            return '';
        }

        $name   = implode('', $paramArray);
        $class  = "\\App\\Entity\\Principal\\" . $name;
        $exists = \class_exists($class);

        if ($exists === false) {
            array_shift($paramArray);
            return $this->getClassName($paramArray);
        }

        return $class;
    }

    /**
     * Instancia os relacionamentos
     */
    private function makeRelations(&$query, &$relations, $queryAlias)
    {
        $splitRelations = [];
        foreach ($relations as $relationString) {
            $relations    = explode('.', $relationString);
            $lastRelation = $queryAlias;
            foreach ($relations as $key => $relation) {
                $query->addSelect($relation);
                $query->leftJoin("$lastRelation.$relation", $relation);
                $lastRelation     = $relation;
                $splitRelations[] = $relation;
            }
        }

        $relations = $splitRelations;
    }

    /**
     * Aplica os filtros
     */
    private function makeFilters(&$query, $filters, $queryAlias, $queryJoins=[])
    {
        $whereClauses = [];
        foreach ($filters as $filter) {
            $field = $filter['field'];
            if (strpos($field, '.') === false) {
                $field = "$queryAlias.$field";
            } else {
                $exploded = explode('.', $field);

                // Se houverem mais de dois níveis, ex.: "livro_biblioteca_exemplar.livro_biblioteca.nome", usa-se apenas os dois últimos nomes
                if (count($exploded) > 2) {
                    $last  = count($exploded) - 1;
                    $field = $exploded[$last - 1] . "." . $exploded[$last];
                }

                if (in_array($exploded[0], $queryJoins) === false) {
                    $join = [ $exploded[0] ];
                    $this->makeRelations($query, $join, $queryAlias);
                }
            }

            $criteria = $filter['criteria'];
            $value    = $filter['value'];
            $varName  = str_replace('.', '_', $field) . "_" . uniqid();
            if ($criteria === 'LIKE') {
                $value          = str_replace('^', '%', $value);
                $whereClauses[] = $query->expr()->andX("$field $criteria :$varName");
            } else {
                $whereClauses[] = $query->expr()->andX("$field $criteria (:$varName)");
            }

            if ($value === '$CURRENT_FRANCHISE') {
                $value = \App\Helper\VariaveisCompartilhadas::$franqueadaID;
            }

            $query->setParameter($varName, $value);
        }//end foreach

        return $whereClauses;
    }

    /**
     * Executa consultas de relacionamentos
     */
    private function fillRelations(&$payload, $dataCollection)
    {
        foreach ($dataCollection as $dataIndex => $data) {
            \preg_match('/(.*)_id$/', $dataIndex, $matches);
            if (isset($matches[1]) === true) {
                $class = $this->getClassName(explode('_', ucwords($matches[1], '_')));
                $find  = $this->manager->getRepository($class)->find($data);

                if (is_null($find) === false) {
                    $payload['data'][$matches[1]] = $find;
                }
            }
        }
    }

    /**
     * Executa transformações de dados para transformar no JSON
     */
    private function transformData($instance)
    {
        foreach ($instance as $instanceField => $value) {
            if (is_array($value) === true) {
                $instance[$instanceField] = $this->transformData($value);
            }

            if ($value instanceof \DateTime) {
                $instance[$instanceField] = $value->format('Y-m-d\TH:i:sP');
            }
        }

        return $instance;
    }

    /**
     * @Route("/api/magic", methods={"GET"}, name="magic")
     */
    public function index(Request $request)
    {
        $payload    = $request->query->all();
        $entity     = $payload['entity'];
        $statusCode = Response::HTTP_OK;
        $response   = [ 'entity' => $entity ];

        $metadata = \App\Entity\MagicEntity::getMetadata($this->manager, $entity);
        if ($metadata === false) {
            $statusCode    = Response::HTTP_NOT_FOUND;
            $errorMessages = [ 'Objeto de metadados não encontrado' ];
            return new Response(\json_encode($response), 404);
        }

        $query      = $this->manager->createQueryBuilder();
        $queryAlias = 'e';
        $query->select($queryAlias);
        $query->from($entity, $queryAlias);

        $hasBranchRelation = false;
        $relationAliases   = [];
        if (isset($payload['with']) === true) {
            $relationAliases = $payload['with'];
            $this->makeRelations($query, $relationAliases, $queryAlias);

            if (in_array('franqueada', $relationAliases) === true) {
                $hasBranchRelation = true;
            }
        }

        $hasFranchise = false;
        $countFields  = count($metadata['fields']);
        for ($index = 0; $index < $countFields; $index++) {
            if ($metadata['fields'][$index]['name'] === 'franqueada') {
                $hasFranchise = true;
                break;
            }
        }

        if ($hasFranchise === true) {
            if ($hasBranchRelation === true) {
                $str = 'franqueada.id = :franqueada';
            } else {
                $str = 'e.franqueada = :franqueada';
            }

            if (isset($payload['withFranchisingData']) === true) {
                $query->andWhere(
                    $query->expr()->orX($str, 'franqueada.franqueadora = 1')
                );
            } else {
                $query->andWhere($str);
            }

            $query->setParameter('franqueada', \App\Helper\VariaveisCompartilhadas::$franqueadaID);
        }

        $methods = get_class_methods($entity);
        if (in_array('customIndexQuery', $methods) === true) {
            // $entity::customIndexQuery($query, $this->manager);
        }

        if (isset($payload['where']) === true) {
            $whereOperator = 'andX';
            if (isset($payload['whereOperator']) === true && $payload['whereOperator'] === 'OR') {
                $whereOperator = 'orX';
            }

            $countWheres = count($payload['where']);
            for ($index = 0; $index < $countWheres; $index++) {
                if ($payload['where'][$index]['value'] === '$CURRENT_FRANCHISE') {
                    $field    = $payload['where'][$index]['field'];
                    $criteria = $payload['where'][$index]['criteria'];
                    $query->andWhere("$field $criteria :franqueada");

                    if (is_null($query->getParameter('franqueada')) === true) {
                        $query->setParameter('franqueada', \App\Helper\VariaveisCompartilhadas::$franqueadaID);
                    }

                    unset($payload['where'][$index]);

                    break;
                }
            }

            $query->andWhere(
                $query->expr()->$whereOperator(
                    ...$this->makeFilters($query, $payload['where'], $queryAlias, $relationAliases)
                )
            );
        }//end if

        $paginatorOptions = [];
        if (isset($payload['sort_column']) === true) {
            $sortColumn = $payload['sort_column'];
            if (strpos($sortColumn, '.') === false) {
                $sortColumn = "$queryAlias.$sortColumn";
            }

            $direction = 'ASC';
            if (isset($payload['sort_direction']) === true) {
                $direction = $payload['sort_direction'];
            }

            $query->orderBy($sortColumn, $direction);
            $paginatorOptions[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $paginatorOptions[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        $pageSize = 50;
        if (isset($payload['doNotPaginate']) === true) {
            $pageSize = 500;
        }

        $page = 1;
        if (isset($payload['page']) === true) {
            $page = $payload['page'];
        }

        $result = \App\Helper\FunctionHelper::montaPaginatorPaginacao($query, $page, $pageSize, $paginatorOptions);

        $items      = $result->getItems();
        $countItems = count($items);
        for ($index = 0; $index < $countItems; $index++) {
            $instance      = $items[$index];
            $items[$index] = $this->transformData($instance);
        }

        $response['data']  = $items;
        $response['total'] = $result->getTotalItemCount();

        return new Response(\json_encode($response), $statusCode);
    }

    /**
     * @Route("/api/magic/{id}", methods={"GET"}, name="magic_show")
     */
    public function show($id, Request $request)
    {
        $payload  = $request->query->all();
        $entity   = $payload['entity'];
        $metadata = \App\Entity\MagicEntity::getMetadata($this->manager, $entity);

        $statusCode = Response::HTTP_OK;
        $response   = [ 'entity' => $entity ];

        $query      = $this->manager->createQueryBuilder();
        $queryAlias = 'e';
        $query->select($queryAlias);
        $query->from($entity, $queryAlias);

        $hasBranchRelation = false;
        $relationAliases   = [];
        if (isset($payload['with']) === true) {
            $relationAliases = $payload['with'];
            $this->makeRelations($query, $relationAliases, $queryAlias);

            if (in_array('franqueada', $relationAliases) === true) {
                $hasBranchRelation = true;
            }
        }

        $hasFranchise = false;
        $countFields  = count($metadata['fields']);
        for ($index = 0; $index < $countFields; $index++) {
            if ($metadata['fields'][$index]['name'] === 'franqueada') {
                $hasFranchise = true;
                break;
            }
        }

        if ($hasFranchise === true) {
            if ($hasBranchRelation === true) {
                $query->andWhere('franqueada.id = :franqueada');
            } else {
                $query->andWhere('e.franqueada = :franqueada');
            }

            $query->setParameter('franqueada', \App\Helper\VariaveisCompartilhadas::$franqueadaID);
        }

        $query->andWhere('e.id = :id')->setParameter('id', $id);

        $instance = $query->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (is_null($instance) === true) {
            $statusCode         = Response::HTTP_NOT_FOUND;
            $errorMessages      = [ 'Objeto não encontrado' ];
            $response['errors'] = $errorMessages;
        } else {
            foreach ($instance as $instanceField => $value) {
                if ($value instanceof \DateTime) {
                    $instance[$instanceField] = $value->format('Y-m-d\TH:i:sP');
                }
            }

            $response['data'] = $instance;
        }

        return new Response(\json_encode($response), $statusCode);
    }

    /**
     * @Route("/api/magic", methods={"POST"}, name="magic_create")
     */
    public function create(Request $request)
    {
        $payload        = $request->request->all();
        $dataCollection = $payload['data'];
        $statusCode     = Response::HTTP_OK;
        $response       = [ 'entity' => $payload['entity'] ];

        $instance           = \App\Factory\GeneralORMFactory::criar($payload['entity']);
        $franqueadaInstance = $this->manager->getRepository('App\\Entity\\Principal\\Franqueada')->find(\App\Helper\VariaveisCompartilhadas::$franqueadaID);

        if (isset($payload['data']['franqueada']) === true) {
            $payload['data']['franqueada'] = $franqueadaInstance;
        }

        $metadata = \App\Entity\MagicEntity::getMetadata($this->manager, $payload['entity']);
        foreach ($metadata['fields'] as $field) {
            if ($field['type'] === 'datetime') {
                $date = \App\Helper\FunctionHelper::formataCampoDateTimeJS($payload['data'][$field['name']]);
                if ($date === false) {
                    if ($field['default'] === 'CURRENT_TIMESTAMP') {
                        $date = new \DateTime();
                    } else {
                        $date = null;
                    }
                }

                $payload['data'][$field['name']] = $date;
            } else if ($field['type'] === 'oneToMany' && isset($payload['data'][$field['name']]) === true) {
                for ($index = 0; $index < count($payload['data'][$field['name']]); $index++) {
                    $relatedInstanceData = $payload['data'][$field['name']][$index];

                    if (isset($relatedInstanceData['franqueada']) === true) {
                        $relatedInstanceData['franqueada'] = $franqueadaInstance;
                    }

                    $childEntityMetadata = \App\Entity\MagicEntity::getMetadata($this->manager, $field['targetEntity']);
                    $parentEntityKey     = array_search($payload['entity'], array_column($childEntityMetadata['fields'], 'targetEntity'));
                    if ($parentEntityKey !== false) {
                        $parentEntityFieldInRelatedMetadata = $childEntityMetadata['fields'][$parentEntityKey];
                        $relatedInstanceData[$parentEntityFieldInRelatedMetadata['name']] = $instance;
                    }

                    $relatedInstance = null;
                    if (is_null($relatedInstanceData['id']) === false && empty($relatedInstanceData['id']) === false) {
                        $relatedInstance = $this->manager->getRepository($field['targetEntity'])->find($relatedInstanceData['id']);
                    } else {
                        if (isset($relatedInstanceData['_removed']) === false || $relatedInstanceData['_removed'] === false) {
                            $relatedInstance = \App\Factory\GeneralORMFactory::criar($field['targetEntity']);
                            $this->manager->persist($relatedInstance);
                        }
                    }

                    if (is_null($relatedInstance) === false) {
                        $this->fillRelations($relatedInstanceData, $childEntityMetadata);
                        \App\Helper\FunctionHelper::setParams($relatedInstanceData, $relatedInstance);
                        $removed = false;

                        if (isset($relatedInstanceData['_removed']) === true && empty($relatedInstanceData['_removed']) === false) {
                            $this->manager->remove($relatedInstance);
                            $removed = true;
                        }

                        $entityPath = explode('\\', $field['targetEntity']);
                        if ($removed === false && isset($relatedInstanceData['_added']) === true && empty($relatedInstanceData['_added']) === false) {
                            $function = 'add' . end($entityPath);
                            $instance->{$function}($relatedInstance);
                        }
                    }
                }//end for
            } else if ($field['type'] === 'manyToMany' && isset($payload['data'][$field['name']]) === true) {
                for ($index = 0; $index < count($payload['data'][$field['name']]); $index++) {
                    $relatedInstanceData = $payload['data'][$field['name']][$index];
                    $relatedInstance     = null;
                    if (is_null($relatedInstanceData['id']) === false && empty($relatedInstanceData['id']) === false) {
                        $relatedInstance = $this->manager->getRepository($field['targetEntity'])->find($relatedInstanceData['id']);
                    }

                    $entityPath = explode('\\', $payload['entity']);
                    $function   = null;

                    if (isset($relatedInstanceData['_added']) === true && empty($relatedInstanceData['_added']) === false) {
                        $function = "add" . end($entityPath);
                    }

                    if (isset($relatedInstanceData['_removed']) === true && empty($relatedInstanceData['_removed']) === false) {
                        $function = "remove" . end($entityPath);
                    }

                    if (is_null($function) === false) {
                        $relatedInstance->{$function}($instance);
                    }
                }//end for
            }//end if
        }//end foreach

        $this->fillRelations($payload, $dataCollection);
        \App\Helper\FunctionHelper::setParams($payload['data'], $instance);

        $methods = get_class_methods($payload['entity']);
        if (in_array('onCreate', $methods) === true) {
            $result = $payload['entity']::onCreate($instance, $this->manager);

            if (empty($result['errors']) === false) {
                $statusCode    = Response::HTTP_BAD_REQUEST;
                $errorMessages = [
                    'Erro ao criar o registro',
                    $result['errors'],
                ];

                $response['errors'] = $errorMessages;
                return new Response(\json_encode($response), $statusCode);
            }
        }

        try {
            $this->manager->persist($instance);
            $this->manager->flush();
            $response['id'] = $instance->getId();
        } catch (\Exception $e) {
            $statusCode    = Response::HTTP_BAD_REQUEST;
            $errorMessages = [
                'Houve um erro ao salvar o registro',
                $e->getMessage(),
            ];

            $response['errors'] = $errorMessages;
        }

        return new Response(\json_encode($response), $statusCode);
    }

    /**
     * @Route("/api/magic", methods={"PUT"}, name="magic_update")
     */
    public function update(Request $request)
    {
        $payload    = $request->request->all();
        $response   = [ 'entity' => $payload['entity'] ];
        $statusCode = Response::HTTP_OK;

        $dataCollection     = $payload['data'];
        $instance           = null;
        $franqueadaInstance = $this->manager->getRepository('App\\Entity\\Principal\\Franqueada')->find(\App\Helper\VariaveisCompartilhadas::$franqueadaID);

        if (isset($dataCollection['id']) === false) {
            $errorMessages      = [
                'Houve um erro ao atualizar o registro',
                'Registro não encontrado',
            ];
            $response['errors'] = $errorMessages;
        }

        if (isset($response['errors']) === false) {
            $instance = $this->manager->getRepository($payload['entity'])->find($dataCollection['id']);
            if (is_null($instance) === true) {
                $errorMessages      = [
                    'Houve um erro ao atualizar o registro',
                    'Registro não encontrado',
                ];
                $response['errors'] = $errorMessages;
            }
        }

        if (isset($response['errors']) === true) {
            $statusCode = Response::HTTP_NOT_FOUND;
            return new Response(\json_encode($response), $statusCode);
        }

        $metadata = \App\Entity\MagicEntity::getMetadata($this->manager, $payload['entity']);
        foreach ($metadata['fields'] as $field) {
            if ($field['type'] === 'datetime') {
                $date = \App\Helper\FunctionHelper::formataCampoDateTimeJS($payload['data'][$field['name']]);
                if ($date === false) {
                    $date = null;
                }

                $payload['data'][$field['name']] = $date;
            } else if ($field['type'] === 'oneToMany' && isset($payload['data'][$field['name']]) === true) {
                for ($index = 0; $index < count($payload['data'][$field['name']]); $index++) {
                    $relatedInstanceData = $payload['data'][$field['name']][$index];

                    if (isset($relatedInstanceData['franqueada']) === true) {
                        $relatedInstanceData['franqueada'] = $franqueadaInstance;
                    }

                    $childEntityMetadata = \App\Entity\MagicEntity::getMetadata($this->manager, $field['targetEntity']);
                    $parentEntityKey     = array_search($payload['entity'], array_column($childEntityMetadata['fields'], 'targetEntity'));
                    if ($parentEntityKey !== false) {
                        $parentEntityFieldInRelatedMetadata = $childEntityMetadata['fields'][$parentEntityKey];
                        $relatedInstanceData[$parentEntityFieldInRelatedMetadata['name']] = $instance;
                    }

                    $relatedInstance = null;
                    if (is_null($relatedInstanceData['id']) === false && empty($relatedInstanceData['id']) === false) {
                        $relatedInstance = $this->manager->getRepository($field['targetEntity'])->find($relatedInstanceData['id']);
                    } else {
                        if (isset($relatedInstanceData['_removed']) === false || $relatedInstanceData['_removed'] === false) {
                            $relatedInstance = \App\Factory\GeneralORMFactory::criar($field['targetEntity']);
                            $this->manager->persist($relatedInstance);
                        }
                    }

                    if (is_null($relatedInstance) === false) {
                        $this->fillRelations($relatedInstanceData, $childEntityMetadata);
                        \App\Helper\FunctionHelper::setParams($relatedInstanceData, $relatedInstance);

                        if (isset($relatedInstanceData['_removed']) === true && empty($relatedInstanceData['_removed']) === false) {
                            $this->manager->remove($relatedInstance);
                        }

                        $entityPath = explode('\\', $field['targetEntity']);
                        if (isset($relatedInstanceData['_added']) === true && empty($relatedInstanceData['_added']) === false) {
                            $function = 'add' . end($entityPath);
                            $instance->{$function}($relatedInstance);
                        }
                    }
                }//end for
            } else if ($field['type'] === 'manyToMany' && isset($payload['data'][$field['name']]) === true) {
                for ($index = 0; $index < count($payload['data'][$field['name']]); $index++) {
                    $relatedInstanceData = $payload['data'][$field['name']][$index];
                    $relatedInstance     = null;
                    if (is_null($relatedInstanceData['id']) === false && empty($relatedInstanceData['id']) === false) {
                        $relatedInstance = $this->manager->getRepository($field['targetEntity'])->find($relatedInstanceData['id']);
                    }

                    $entityPath = explode('\\', $payload['entity']);
                    $function   = null;

                    if (isset($relatedInstanceData['_added']) === true && empty($relatedInstanceData['_added']) === false) {
                        $function = "add" . end($entityPath);
                    }

                    if (isset($relatedInstanceData['_removed']) === true && empty($relatedInstanceData['_removed']) === false) {
                        $function = "remove" . end($entityPath);
                    }

                    if (is_null($function) === false) {
                        $relatedInstance->{$function}($instance);
                    }
                }//end for
            }//end if
        }//end foreach

        $this->fillRelations($payload, $dataCollection);
        \App\Helper\FunctionHelper::setParams($payload['data'], $instance);

        $methods = get_class_methods($payload['entity']);
        if (in_array('onUpdate', $methods) === true) {
            $result = $payload['entity']::onUpdate($instance, $this->manager);

            if (empty($result['errors']) === false) {
                $statusCode    = Response::HTTP_BAD_REQUEST;
                $errorMessages = [
                    'Erro ao atualizar o registro',
                    $result['errors'],
                ];

                $response['errors'] = $errorMessages;
                return new Response(\json_encode($response), $statusCode);
            }
        }

        try {
            $this->manager->flush();
            $response['id'] = $instance->getId();
        } catch (\Exception $e) {
            $statusCode    = Response::HTTP_BAD_REQUEST;
            $errorMessages = [
                'Houve um erro ao atualizar o registro',
                $e->getMessage(),
            ];

            $response['errors'] = $errorMessages;
        }

        return new Response(\json_encode($response), $statusCode);
    }

    /**
     * @Route("/api/metadata", methods={"GET"}, name="metadata")
     */
    public function metadata(Request $request)
    {
        $payload  = $request->query->all();
        $response = \App\Entity\MagicEntity::getMetadata($this->manager, $payload['entity'], $payload['view']);

        return new Response(\json_encode($response));
    }


}
