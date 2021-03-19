# futtables
Script PHP para recuperar tabelas e rodadas dos principais campeonatos de futebol do Brasil.

### Indice

* Sobre o código
* Instalação
* Uso
	 * Links
	 * Tipos disponiveis
	 	* Campeonato Brasileiro
		 * Campeonato Paulista
		 * Libertadores da América
* Implementação
* Págnas fonte
* Contato

## Sobre o código
O futtables foi inteiramente desenvolvido em PHP 8, mas também é compativel com PHP 7. Nomes de funções e variaveis estão em Inglês Americano, porém todos os comentários em blocos de código estão em Português do Brasil.

Como o futtables foi feito para ser usado em tabelas client-side (frontend), ele retorna os dados na forma de um documento JSON encodado em UTF-8, e preferencialmente deve ser recuperado usando AJAX ou XMLHttpRequest, sendo o primeiro disponivel apenas com uso do framework JQuery, e ambos na linguagem de programação Javascript.

O código ficou dividido entre dois arquivos: **campeonatos.php** e **FutTables.php**

* campeonatos.php
	Esse arquivo é responsavel por apresentar os retornos das páginas. Ele recebe 2 parametros via GET (URL) e retorna os dados de acordo com o que foi solicitado.

* FutTables.php
	Essa é a classe que recupera os dados. Dentro das suas funções foram usados os métodos de extração de conteudo mais adequados ao que as páginas fonte retornavam.

## Instalação
Para instalar o futtables, basta baixar ou clonar esse repositório e copiar os arquivos para alguma hospedagem.

* Comando para o Git
```git clone https://github.com/eEmmyfuttables```

## Links
Assim que você colocar a pasta do futtables em sua hospedagem, poderá acessar ela usando o seguinte caminho ```http://www.seusite.com/caminho/futtables/campeonato.php```. Nesse primeiro momento você poderá ver o retorno que o script dá em caso de erro (Nota: é recomendado ter instalado uma extensão que formate JSON no seu navegador).

O erro que você está vendo se deve ao fato de não terem sido passados os parametros corretos para o funcionamento do futtables, que são **campeonato** e **tipo**.

#### Campeonato
Esse parametro define os dados de qual campeonato você deseja recuperar, e pode assumir três valores: brasileiro, paulista e libertadores. Claramente, em ordem, são Campeonato Brasileiro, Campeonato Paulista e Libertadores da América

#### Tipo
Já esse, define o que você quer retornar do campeonato, rodadas ou a tabela. Para saber quais tipos estão disponiveis para cada campeonato, consulte a seção seguinte.

#### Como usar os parametros
Se você deseja ver o documento JSON de uma solicitação valida, precisa apenas alterar sua URL para http://www.seusite.com/caminho/futtables/campeonato.php?campeonato=campeonato&tipo=tipo, alterando é claro os valores para o campeonato e tipo desejados.

## Tipos disponiveis

#### Campeonato Brasileiro
* Tabela
* Rodadas

#### Campeonato Paulista
* Tabela
* Rodadas

#### Libertadores da América
* Rodadas

## Como implementar
A implementação deve ser feita via solicitação AJAX ou XMLHttpRequest, ambos no JavaScript (Nota: AJAX só está disponivel no framework JQuery).

Não vou colocar aqui o código para a solicitação por que isso pode ser encontrado em milhões de sites na internet. Apenas atenho a dois detalhes
* Os parametros não devem ser passados diretamente na URL da solicitação, e sim como parametros do tipo GET, que ambos os métodos podem definir separadamente, evitando assim qualquer erro na hora do JavaScript parsear a URL.
* Se a solicitação chegar na página-alvo, mesmo que esta retorne uma mensagem de erro, o resultado da solicitação será interpretado como bem-sucedida. Para verificar se houveram erros, consulte a existencia do parametro error do objeto de resposta

## Páginas fonte
As páginas fonte a seguir foram usadas para extrair os dados, e serão atualizadas conforme os campeonatos, assim como o proprio ano do campeonato, não sendo necessário altera-las, dado que isso quebraria a extração de dados.

Ao todo, foram 4 páginas usadas, são elas
* https://www.brasileirao.com.br
* https://www.gazetaesportiva.com/campeonatos/brasileiro-serie-a/
* https://www.gazetaesportiva.com/campeonatos/paulista/
* https://gauchazh.clicrbs.com.br/esportes/tabelas/libertadores

## Contato

* Email para contato: <mailto:aou-emmy@outlook.com>
* Telefone para contato: +55 (11) 95837-8163
