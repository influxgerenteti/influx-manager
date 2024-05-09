<?php
namespace App\Entity;

class MagicEntity extends \ReflectionClass
{

    private static $dataToRetrieve = [
        'type',
        'label',
        'length',
        'mask',
        'showOnAdd',
        'showOnUpdate',
        'showOnBrowse',
        'canUpdate',
        'targetEntity',
        'nullable',
        'descriptionColumn',
        'valueColumn',
        'findType',
        'findQuery',
        'queryColumn',
        'listViewOrder',
        'formViewOrder',
        'listViewClass',
        'formViewClass',
        'formViewBreakAfter',
        'oneToManyTableColumns',
        'manyToManyTableColumns',
        'selectOptions',
        'view',
        'required',
        'default',
        'format',
        'canOrderBy',
        'orderColumn',
    ];

    private static function getFieldsData($comment)
    {
        $result = [];
        $dataToRetrieveLength = count(self::$dataToRetrieve);

        for ($index = 0; $index < $dataToRetrieveLength; $index++) {
            $metadataProp          = self::$dataToRetrieve[$index];
            $result[$metadataProp] = null;
            // format property="value"
            \preg_match("/$metadataProp=\"([-\w\d\s,\.\{\}\[\]:\'\\\\]+)\"/u", $comment, $matches1, PREG_OFFSET_CAPTURE);
            // format property=value
            \preg_match("/$metadataProp=([-\w\d\s\.\\\\]+)/u", $comment, $matches2, PREG_OFFSET_CAPTURE);
            // format "property": "value"
            \preg_match("/\"$metadataProp\": \"([-\w\d\s\.\\\\]+)\"/u", $comment, $matches3, PREG_OFFSET_CAPTURE);
            $matches = \array_merge($matches1, $matches2, $matches3);

            // No caso de relacionamentos o campo type não é setado, então buscamos os relacionamentos
            if ($metadataProp === 'type' && isset($matches[1]) === false) {
                \preg_match("/(ManyToOne|OneToOne|ManyToMany|OneToMany)/u", $comment, $matches, PREG_OFFSET_CAPTURE);
                if (isset($matches[1]) === true) {
                    $matches[1][0] = lcfirst($matches[1][0]);
                }
            }

            if (isset($matches[1]) === true) {
                $data = $matches[1][0];
                if ($data === 'true') {
                    $data = true;
                } else if ($data === 'false') {
                    $data = false;
                }

                if ($metadataProp === 'oneToManyTableColumns' || $metadataProp === 'manyToManyTableColumns') {
                    $data = explode(',', $data);
                }

                $result[$metadataProp] = $data;
            }
        }//end for

        return $result;
    }

    private static function getAuxiliarEntities ($fields)
    {
        $auxiliarEntities = [];

        $countFields = count($fields);
        for ($index = 0; $index < $countFields; $index++) {
            if (in_array($fields[$index]['type'], ['oneToMany', 'oneToOne', 'manyToOne', 'manyToMany']) === true) {
                $class = $fields[$index]['targetEntity'];
                $ref   = new \ReflectionClass($class);
                $props = $ref->getProperties();

                $auxiliarEntities[$class] = [];

                for ($i = 0, $propIndex = 0; $i < count($props); $i++) {
                    $prop = $props[$i]->name;
                    if (in_array($prop, ['browseJoins', 'createJoins', 'updateJoins']) === true) {
                        continue;
                    }

                    $comm = $ref->getProperty($prop)->getDocComment();

                    $auxiliarEntities[$class][$propIndex]         = self::getFieldsData($comm);
                    $auxiliarEntities[$class][$propIndex]['name'] = $prop;
                    $propIndex++;
                }
            }
        }//end for

        return $auxiliarEntities;
    }

    public static function getMetadata ($manager, $class, $view=null)
    {
        $ref    = new \ReflectionClass($class);
        $props  = $ref->getProperties();
        $result = [
            'meta'             => [],
            'fields'           => [],
            'browseJoins'      => [],
            'createJoins'      => [],
            'updateJoins'      => [],
            'auxiliarEntities' => [],
            'views'            => [],
            'filters'          => [],
            'permissions'      => [],
        ];

        $moduleRepository = $manager->getRepository('App\\Entity\\Principal\\Modulo');
        $userRepository   = $manager->getRepository('App\\Entity\\Principal\\Usuario');

        $findModule = $moduleRepository->findOneBy(['entity' => $class]);
        if (is_null($findModule) === true) {
            return false;
        }

        $result['meta'] = [
            'id'   => $findModule->getId(),
            'name' => $findModule->getNome(),
            'url'  => $findModule->getUrl(),
        ];

        for ($i = 0, $index = 0; $i < count($props); $i++) {
            $prop = $props[$i]->name;
            if (in_array($prop, ['browseJoins', 'createJoins', 'updateJoins', 'views', 'filters']) === true) {
                $result[$prop] = $ref->getStaticPropertyValue($prop);
                continue;
            }

            $comm = $ref->getProperty($prop)->getDocComment();

            $result['fields'][$index]         = self::getFieldsData($comm);
            $result['fields'][$index]['name'] = $prop;
            $index++;
        }

        if (is_null($view) === false && ($view === 'list' || $view === 'form')) {
            usort(
                $result['fields'],
                function ($itemA, $itemB) use ($view) {
                    $viewVar = $view . 'ViewOrder';
                    if ($itemA[$viewVar] === $itemB[$viewVar]) {
                        return 0;
                    }

                    if ($itemA[$viewVar] < $itemB[$viewVar]) {
                        return -1;
                    }

                    return 1;
                }
            );
        }

        // Get related entities
        $result['auxiliarEntities'] = self::getAuxiliarEntities($result['fields']);

        // Get the user permissions
        $user  = $userRepository->find(\App\Helper\VariaveisCompartilhadas::$usuarioID);
        $roles = $user->getPapels()->map(
            function ($role) {
                return $role->getId();
            }
        );

        $permissionParams = [
            \App\Helper\ConstanteParametros::CHAVE_USUARIO => $user->getId(),
            \App\Helper\ConstanteParametros::CHAVE_MODULO  => $findModule->getId(),
            \App\Helper\ConstanteParametros::CHAVE_PAPEL   => $roles,
        ];

        $module       = $moduleRepository->buscarPermissaoPorModulo($permissionParams);
        $countActions = count($module['acaoSistemas']);

        for ($i = 0; $i < $countActions; $i++) {
            $acao = $module['acaoSistemas'][$i];

            if (isset($module['modulo_papel_acao']) === true) {
                $roleHasPermission = array_search($acao['id'], array_column($module['modulo_papel_acao'], 'acao_sistema_id')) !== false;
                if ($roleHasPermission === true) {
                    $module['acaoSistemas'][$i]['has_permission'] = true;
                    continue;
                }
            }

            $userHasPermission = array_search($acao['id'], array_column($module['moduloUsuarioAcaos'], 'acao_sistema_id')) !== false;
            if ($userHasPermission === true) {
                $module['acaoSistemas'][$i]['has_permission'] = true;
                continue;
            }

            $module['acaoSistemas'][$i]['has_permission'] = false;
        }

        $result['permissions'] = $module['acaoSistemas'];

        return $result;
    }


}
