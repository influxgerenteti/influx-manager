# README #

Projeto influx manager

Plataforma Synfony

## Build ##

```
composer install 

php -S localhost:8000 -t public/

```




## Aceesso ##

### BETA ###

URL|USER|PASS
---|---|---
beta.manager.influx.com.br|?|?





## Magic ##

## Como Funciona
Magic tem o foco de ser declarativo e serve para abstrair o desenvolvimento de funcionalidades simples do sistema, extraindo metadados das entidades, servindo uma API REST e construindo interfaces sem a necessidade de tocar em lógicas genéricas.

Essa abstração é vantajosa pois toma-se muito tempo para construir coisas simples no modelo isolado:

Para comparação, se o módulo de Empréstimo de Biblioteca fosse feito da maneira padrão, seria mais ou menos:

* Back-end:
  * Entidade e Repositório - Com as lógicas de consultas e filtros no repositório;
  * Façade - Com as lógicas para listar, criar e editar um empréstimo;
  * Controller - Com as funções de listar, criar e editar um empréstimo (levar em consideração todas as declarações de parâmetros);
  * BO (opcional).
* Front-end:
  * As views de Lista e Formulário - com os filtros na listagem;
  * A store para manipulação dos dados;
  * Rota para acesso ao módulo.

Já com o Magic, foi utilizado todo o seu padrão, que é utilizado em todos os módulos usando Magic:
* Back-end:
  * MagicEntity (para extração de metadados);
  * MagicController (para o REST);
* Front-end:
  * FormView e ListView padrões;

Dentre as vantagens:
* No front-end componentes sempre funcionarão da mesma forma (não teremos mais um datepicker que não funciona, um campo requerido que não fica vermelho);
* Ainda no front, se precisar de views mais elaboradas é possível criar uma CustomView;
* No back-end, elimina-se toda a lógica chata e muitas vezes falha de declarações de parâmetros e funcionalidades iguais que fazem coisas diferentes em diferentes módulos;
* Não é limitadas as funcionalidades do back-end: quando precisar de outras lógicas no decorrer de criar/atualizar, por exemplo, pode-se chamar funções estáticas que fazem o que necessita (façades, BOs, etc).

## Declarações Nas Entidades

Para que o Magic funcione da forma desejada pode-se usar de várias configurações, todas declaradas na devida entidade.
Assumi como padrão declarar (dentro do docblock, aquele comentário da coluna) um linha após a `@Column|@ManyToOne|etc`, dentro de parenteses: `* (label="Exemplo", required="true")`.

Para referência exata de como utilizar as configurações, ver `src/Entity/Principal/EmprestimoBiblioteca.php` e `src/Entity/Principal/LivroBiblioteca.php`.

### Configurações:
* `type` - O tipo do campo (geralmente já está declarado na coluna, não é necessário declarar)
* `label` - A label do campo (<label>);
* `length` - Quando string, é o tamanho máximo do campo;
* `mask` - Aplicar máscara no campo (aplicará o `v-mask`);
* `showOnAdd` - Se deve ser mostrado no formulário de `/adicionar`;
* `showOnUpdate` - Se deve ser mostrado no formulário de `/atualizar`;
* `showOnBrowse` - Se deve ser mostrado na lista de itens;
* `canUpdate` - Se o campo pode ser atualizado (mostrará `disabled` se `true`);
* `required` - Se o campo é obrigatório;
* `default` - Valor padrão do campo;
* `format` - Formato para exibir campo (quando é para formatar um número para moeda por exemplo) - são suportados: `date, datetime e currency`;
* `canOrderBy` - Usado na ListView para saber se pode ordenar a lista por este campo;
* `orderColumn` - Qual coluna no banco que deve ordenar;
* `selectOptions` - Quando se precisa de um `<select>` com opções fixar, passá-las aqui como JSON ('' ao invés de "");
* `listViewOrder` - Ordem desta coluna na ListView;
* `formViewOrder` - Ordem desta coluna na FormView;
* `listViewClass` - Classe adicional (string separada por espaços para mais de uma) desta coluna na ListView - **Usar para definir o tamanho da coluna - col-md-6, por exemplo**;
* `formViewClass` - Classe adicional (string separada por espaços para mais de uma) desta coluna na FormView - **Usar para definir o tamanho da coluna - col-md-6, por exemplo**;
* `formViewBreakAfter` - Boleano para forçar a linha a quebrar após esta coluna;
* `nullable` - Se o campo pode ser nulo ou não - não possui validação no front-end, é mais garantido usar o `required`;
* `targetEntity` - Quando for um relacionamento - **É preenchido pelo Symfony na criação da tabela**;
* `descriptionColumn` - Quando for um relacionamento, preencher aqui com a coluna da tabela relacionada para ser exibida tanto na lista como no formulário (typeahead, select), podem ser várias, separando-as por vírgula e com até 2 níveis (não tenho certeza) de profundidade;
* `descriptionColumn` - Quando for um relacionamento, preencher aqui com a coluna da tabela que será salva no banco, geralmente e fallback para `id`;
* `findType` - Quando for um relacionamento, deve ser `typeahead` ou `select`` - usado apenas em formulários;
* `findQuery` - Quando for um relacionamento, pode-se passar este parâmetro para informar mais filtros no select - **Até onde vi eu criei e não usei, se possível evitar para removermos no futuro**;
* `queryColumn` - Quando for um relacionamento e `findType === 'typeahead'` é usado para comparar a string digitada com LIKE nesta coluna;
* `oneToManyTableColumns` - Na FormView, estas são as colunas para apresentar da entidade relacionada;
* `manyToManyTableColumns` - Na FormView, estas são as colunas para apresentar da entidade relacionada;

Existem ainda alguns campos estáticos que são consultados na Entidade para personalizar:
* `browseJoins` - lista de nomes dos relacionamentos a trazer junto no select dos itens na ListView;
* `updateJoins` - lista de nomes dos relacionamentos a trazer junto no select do item a ser atualizado na FormView/atualizar;
* `filters` - lista de filtros, agrupados por `quick` e `advanced`;
  * Atentar que quando o filtro for `LIKE`, precisa fazer com `^` ao invés de `%`, mas o funcionamento é o mesmo.
* Funções `onCreate` e `onUpdate` que são chamadas exatamente no momento que levaram o nome, mas antes de ser executado o `flush`;

## Caveats/Bugs conhecidos
* Quando tem uma entidade relacionada, os campos dela não estão sendo obrigatórios;

## Futuro
* Ao invés de consultar os metadados na entidade, ter uma tabela com eles;
* Criar Views para permitir múltiplas views para as mesmas entidades, por exemplo: um formulário quando for na tela e outro diferente quando estiver numa modal;
* Suportar modais para inserir entidades dentro de entidades;

---

## Criação de **Módulo** (Item da Sidebar)

### 1º Passo: Registro do Módulo


> Inserir o registro na tabela **modulo** utilizando o seguinte script SQL:
```
INSERT INTO `modulo` (
  `modulo_pai_id`, -- id do módulo pai (inteiro)
  `nome`, -- Nome a ser exibido (texto)
  `url`, -- Rota a ser acessada (texto)
  `situacao`, -- Status do módulo (A=ativo, I=inativo, R=removido)
  `ordem`, -- Ordem em relação aos de mesmo parentesco (inteiro)
  `exibir_no_menu`, -- Exibição na sidebar (booleano)
  `entity`, -- Onde será executada a função (texto)
  `apenas_franqueadora`, -- apenas franqueadora (booleano)
  `exibir_como_relatorio` -- exibir como um relatorio (booleano)
) VALUES (
  '5',
  'Relatório de Descontos',
  '\/relatorios\/descontos',
  'A',
  NULL,
  '1',
  'App\Entity\Principal\Relatorio',
  '0',
  '1'
);
```

### 2º Passo: Criação das Ações da rota

> É necessário associar ações a serem realizadas na nova rota, para tal utilize o seguinte script SQL para popular a tabela **acao_sistema_modulo**:
```
INSERT INTO `acao_sistema_modulo` (
  `acao_sistema_id`, -- Ação que será realizada
  `modulo_id` -- id do módulo criado, para associar a ação
) VALUES
  ('1', 92), -- Acessar UTILIZE O ID DO MÓDULO CRIADO
  ('2', 92), -- Listar
  ('3', 92), -- Criar
  ('4', 92), -- Editar
  ('5', 92); -- Excluir
```

> As ações listadas são o padrão adotado no projeto inteiro.

### 3º Passo: Associação entre Papel e Ação

```
INSERT INTO `modulo_papel_acao` (
  `papel_id`, -- papel que realizará a ação
  `acao_sistema_id`, -- ação a ser realizada
  `modulo_id` -- id do módulo criado, para associar a ação
) VALUES
  ('2', '1', 92),
  ('2', '2', 92),
  ('2', '3', 92),
  ('2', '4', 92),
  ('2', '5', 92),
  ('4', '1', 92),
  ('4', '2', 92),
  ('4', '3', 92),
  ('4', '4', 92),
  ('4', '5', 92);
```

### 4º Passo: Associação entre Usuario e Ação

```
INSERT INTO `modulo_usuario_acao` (
  `modulo_id`,
  `usuario_id`,
  `acao_sistema_id`
) VALUES
  (92, '62', '1'),
  (92, '62', '2'),
  (92, '62', '3'),
  (92, '62', '4'),
  (92, '62', '5');

```