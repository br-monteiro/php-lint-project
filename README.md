## KR04 PHP Linter

Este projeto foi criado para atender ao cronograma de tarefas das OKRs de oito semanas na *Chaordic Systems*. Porém, pode ser perfeitamente usado, modificado ou adaptado pela comunidade.
O KR04 foi criado para verificar se os códigos PHP do seu projeto estão de acordo com alguns padrões, dentre eles: __Sintaxe, PSR-2, PSR-3__ e __Chaordic Patterns__.

### Dependências

- PHP 5.6+
- Composer

### Instalação

Para instalar o KR04 use o Composer. Esta documentação assume que você já tem o Composer instalado em sua máquina. Caso precise instalar o Composer siga a [documentação oficial](https://getcomposer.org/).
Abaixo é possível observar um exemplo de configuração do arquivo *__composer.json__*.

```json
{
    "require": {
        "phbsis/kr04-php-lint": "^0.5.0"
    },
    "scripts": {
        "post-install-cmd": [
            "php -r \"copy('vendor/phbsis/kr04-php-lint/index.php', 'checker-kr04');\""
        ],
        "post-update-cmd": [
            "php -r \"copy('vendor/phbsis/kr04-php-lint/index.php', 'checker-kr04');\""
        ]
    }
}
```
Após configurar o arquivo *__composer.json__*, rode o comando composer install e aguarde o final da instalação.
```bash
$ composer install
```
Ao final do processo, se tudo ocorrer com sucesso, será possível observar o arquivo __checker-kr04__ na raiz do projeto; este será usado como *executável* da biblioteca KR04.

### Como usar o KR04

Por padrão, a biblioteca já vem com os checadores de Sintaxe, PSR-2, PSR-3 e padrões usados na Chaordic Systems. É importante informar que não foram implementadas todas as regras das PSRs citadas acima. Continuando com as informações de uso, assumindo que você já se encontra no diretório raiz do seu projeto, basta rodar no terminal o comando:
```bash
php checker-kr04
```

### Parâmetros especiais no terminal

O KR04 também possui alguns parâmetros que podem ser passados ao executar a verificação de seus arquivos PHP. Abaixo é possível ver a lista de atributos que já são usados pelo sistema. Caso seja necessário, também é possível receber os parâmetros nas Classes Checkers através da Injeção de Dependência de *KR04\Cli\Commands* que toda Checker possui.

__path__ : Altera o path do diretório base dos arquivos a serem verificados.
```bash
php checker-kr04 --path=./path/to/new/directory/
```
__list__ : Lista todos os Checkers registrados no sistema.
```bash
php checker-kr04 --list
```
__stop__ : Para a execução no primeiro erro encontrado.
```bash
php checker-kr04 --stop
```
__only__ : Executa somente os Checkers passados como parâmetro.
```bash
php checker-kr04 --only=chaordicpatternchecker,syntaxchecker
```
__except__ : Executa todos os demais Checkers, exceto os passados como parâmetro.
```bash
php checker-kr04 --except=syntaxchecker
```

### Criando novas regras de verificação

Novas regras podem ser criadas para atenderem às particularidades dos seus projetos. Para criar uma nova regra é muito simples, porém algumas passos devem ser seguidos para o correto funcionamento do sistema. Uma nova classe de regra (Checker) se parece com o seguinte:
```php
<?php
namespace KR04\Checkers;

use KR04\Checkers\Checker;
use KR04\Files\Loader;
use KR04\Cli\Commands;

class TesteChecker extends Checker
{

    public function __construct(Loader $loader, Commands $commands)
    {
        parent::__construct($loader, $commands);
    }

    protected function check()
    {
        /**
         * Aqui ficarão suas implementações de checagem das regras
         */
        return $this;
    }

    protected function configure()
    {
        /**
         * Aqui ficarão suas implementações de configurações
         * Este método será executado antes do método check()
         */
        return $this;
    }
}
```
Toda classe a ser usada como Checker precisa seguir essas regras:
- Ter o *namespace* setado como __KR04\Checkers__
- Herdar a classe __KR04\Checkers\Checker__
- Implementar o método __protected function configure()__

### Salvando a nova Class Checker

O KR04 usa a PSR-4 em sua implementação e isso proporciona uma flexibilidade de paths para um mesmo namespace. Sendo assim, para que as suas regras não se misturem com as implementadas por default, o *namespace* __KR04\Checkers__ também corresponde ao path __./checkers/__, ou seja, um *diretório chamado de __checkers__ na raiz do seu projeto*. Dessa forma, para salvar suas classes, crie um diretório com este  nome na raiz do projeto e salve suas classes dentro deste diretório.

### Acessando o conteúdo dos arquivos

Toda classe Checker tem acesso ao atributo __$this->loader__. Este atributo guarda a referência para um objeto do Tipo __KR04\Files\Loader__ que contém o método público __$this->loader->getOutput()__, que por sua vez retorna um array com o conteúdo dos arquivos carregados. A hierarquia desse array se parece com o seguinte:
```php
[
    [‘./index.php’] => [
        “string” =>  ‘<?php
              echo “isso é um teste”;’,
        “array” => [
             0 => ‘<?php’,
             1 => ‘    echo “isso é um teste”;’
        ]
    ]
    // mais arquivos...
]
```
Note que este array contém o seguinte padrão:
- Cada índice do array é preenchido com o path do arquivo corrente.
- Cada índice do array, quando acessado, retornar um sub array com o conteúdo do arquivo corrente, sendo um índice deste sub array contendo o arquivo na íntegra (índice string) ou por linhas (índice array). É importante dizer que o índice ‘array’ retorna um sub array, onde cada posição representa uma linha do arquivo.

### Registrando a Nova Classe na fila de execução

Para que sua classe seja executada, a mesma precisa ser registrada no container de execução. Este registro pode ser feito através do método __setChecker(…)__ passando como parâmetro o nome completo da classe no arquivo __checker-kr04__. É possível ver um exemplo de um registro a seguir:
```php
    // código omitido
    $checkerContainer->setChecker(\KR04\Checkers\PsrChecker::class);
    $checkerContainer->setChecker(\KR04\Checkers\ChaordicPatternChecker::class);
    // registrando aqui uma classe TesteChecker
    $checkerContainer->setChecker(\KR04\Checkers\TesteChecker::class);

    // init the verification into the files
    new KR04\Linter($checkerContainer, $commands);
   // código omitido
```

### Ignorando diretórios e arquivos

Nem sempre queremos carregar determinados arquivos, sabendo disso, implementamos algumas formas de facilmente configurar o KR04.
Para realizar este tipo de ação, basta adicionar o path relativo do arquivo como índice no array __$this->ignoreFile__. Este array encontra-se no arquivo __*./vendor/phbsis/kr04-php-lint/src/Config/Config.php*__. Há um método específico que inicia as configurações pra este tipo de ação. Abaixo é possível observar um exemplo de arquivos a serem ignorados:
```php
    // código omitido
    private function configure()
    {
        $this->ignoreFile = [
            $this->rootDirectory . 'header-desktop.php',
            $this->rootDirectory . 'template.php'
        ];
    }
    // código omitido
```
O mesmo procedimento pode ser seguido para ignorar diretórios (e seus subdiretórios), porém, o array responsável por conter essa ‘blacklist’ de diretórios é outro: __$this->ignoreDirectory__. Abaixo é possível observar um exemplo de diretórios a serem ignorados:
```php
    // código omitido
    private function configure()
    {
        $this->ignoreDirectory = [
            $this->rootDirectory . 'api/',
            $this->rootDirectory . 'css/',
            $this->rootDirectory . 'images/'
        ];
    }
    // código omitido
```

### Ignorando blocos de código ou apenas uma linha

Existem tags específicas para indicar um trecho de código a ser ignorado, são elas:
- __@ignore__: Inicia um bloco de código a ser ignorado
- __@ignoreline__: Indica que a linha deve ser ignorada
- __@endignore__: Finaliza um bloco de código ignorado

Abaixo é possível observar alguns exemplos usando as tags citadas:
Uso de *@ignore* e *@endignore*
```php
    // @ignore    aqui inicia a tag
    var_dum([‘teste’]);  // este var_dump será ignorado
    var_dum($this);  // este var_dump será ignorado
    // @endignore   fim da tag
```
Uso do *@ignoreline*
```php
    var_dum($response);  // @ignoreline  esta linha será ignorada
    return $response;
```

### Créditos

Este projeto foi desenvolvido por Edson B S Monteiro - <bruno.monteirodg@gmail.com>
Esta aplicação foi desenvolvida orgulhosamente em uma distribuição Linux. =)

## LAUS DEO