<?php

header("Content-type: text/html; charset=utf-8");

/**
 * Recupera dados sobre os campeonatos de futebol Brasileiro, Paulista e Libertadores.
 */
class FutTables
{
	/**
	 * Retorna uma resposta para solicitações erradas.
	 *
	 * @return Array 
	 */
	public function wrongRequest()
	{
		// Retorna um array contendo a string de erro
		return array(
			'error' => 'Requisição inválida, verifique se a url e os parametros estão corretos e tente novamente.'
		);
	}

	/**
	 * Recupera o conteudo de uma página.
	 *
	 * @param String $targetLink
	 *
	 * @return String $pageContents
	 */
	protected function getPageContents($targetLink)
	{
		// Armazena o conteudo da página
		$pageContents = file_get_contents($targetLink);

		// Retorna o conteudo recuperado, formatado em UTF8
		return $pageContents;
	}

	/**
	 * Recupera a tabela do Brasileirão.
	 *
	 * @return Array $table
	 */
	public function getBRtable()
	{
		// Recupera a página com a tabela
		$pageContents = $this->getPageContents('https://www.brasileirao.com.br/');

		// Retorna apenas o código da tabela
		$tableCode = substr($pageContents, strpos($pageContents, '<div class="box_classificacao">'));
		$tableCode = substr($tableCode, strpos($tableCode, '<tbody>'), strpos($tableCode, '</tbody>'));
		$tableCode = str_replace(substr($tableCode, strpos($tableCode, "<aside")), "", $tableCode);

		// Array para organização de dados
		$table = array(
			0 => [], 1 => [], 2 => [],
			3 => [], 4 => [], 5 => [], 
			6 => [], 7 => [], 8 => [],
			9 => [], 10 => [], 11 => [],
			12 => [], 13 => [], 14 => [],
			15 => [], 16 => [], 17 => [],
			18 => [], 19 => []
		);

		// Controle de loop
		$i = 0;

		// Atributos do documento
		$attrNames = array(
			0 => 'pg',
			1 => 'j',
			2 => 'v',
			3 => 'e',
			4 => 'd',
			5 => 'gp',
			6 => 'gc',
			7 => 'sg'
		);

		// Loop while
		while ($i < 20) { 
			// Garante a inclusão do ultimo time
			if ($i === 19) {
				// Pula para o próximo time
				$tableCode = str_replace("<<<<<<<<<<<<<<<<<<<", "", $tableCode);

				// Pega apenas o source do time atual
				$currentPos = substr($tableCode, strpos($tableCode, "<a") + 1);
				$currentPos = substr($currentPos, strpos($currentPos, '<div class="time__nome-sigla">') + 30);
			}
			else {
				// Pega apenas o source do time atual
				$currentPos = substr($tableCode, strpos($tableCode, "<a") + 1);
				$currentPos = str_replace(substr($currentPos, strpos($currentPos, "<a")), "", $currentPos);
				$currentPos = substr($currentPos, strpos($currentPos, '<div class="time__nome-sigla">') + 30);
			}
			
			// Pega o source do time a ser deletado
			$deletePos = substr($tableCode, strpos($tableCode, "<a") + 1);
			$deletePos = str_replace(substr($deletePos, strpos($deletePos, "<a")), "", $deletePos);

			// Pega o nome do time
			$name = substr($currentPos, strpos($currentPos, '<span class="time__nome">') + 29);
			$name = str_replace(substr($name, strpos($name, '</span>')), '', $name);
			
			// Insere o nome do time no array
			$table[$i]['name'] = utf8_encode($name);

			for ($e=0; $e < 8; $e++) { 
				// Pega o atributo atual do time
				$attr = substr($currentPos, strpos($currentPos, '<span>') + 1);
				$attr = str_replace(substr($attr, strpos($attr, '</span>')), "", $attr);
				
				// Deleta o atributo atual do time
				$currentPos = str_replace($attr, "", $currentPos);

				// Remove a TAG do atributo
				$attr = str_replace("span>", "", $attr);
				$attr = str_replace("<span\n    class=\"time__nome\">", "", $attr);
				$attr = str_replace("span\n    class=\"time__nome\">", "", $attr);

				// Inclui os dados no array
				$table[$i][$attrNames[$e]] = utf8_encode($attr);
			}

			// Remove o time atual da tabela
			$tableCode = str_replace($deletePos, "", $tableCode);

			// Incrementa o controlador de loop
			$i++;
		}

		// Retorna a tabela
		return $table;
	}

	/**
	 * Recupera os jogos das rodadas do Campeonato Brasileiro.
	 *
	 * @return Array $matches
	 */
	public function getBRmatches()
	{
		// Recupera as página com as tabelas
		$pageContents = $this->getPageContents('https://www.gazetaesportiva.com/campeonatos/brasileiro-serie-a/');

		// Inicia um documento DOM
		$doc = new DOMDocument();
		$doc->loadHTML($pageContents);

		// Pega o conteudo HTML do documento
		$xpath = new DOMXPath($doc);

		// Guarda o código fonte das rodadas
		$rounds = array();

		// Guarda os dados das partidas
		$matches = array();

		// Loop for (1 a 38)
		for ($a=1; $a <= 38; $a++) {
			// Separa as rodadas
			$rounds[$a] = $xpath->query("//div[contains(@class,'rodadas_grupo_A_numero_rodada_{$a}')]");
		}

		// Loop for (1 a 38)
		for ($c=1; $c <= 38; $c++) {
			// Loop for (0 a 9)
			for ($i=0; $i < 10; $i++) {
				// Pega a data e o lugar da partida
				$matchDatePlace = $rounds[$c]->item(0);
				$matchDatePlace = $matchDatePlace->getElementsByTagName('li')->item($i);
				$matchDatePlace = $matchDatePlace->getElementsByTagName('span')->item(0)->nodeValue;

				// Separa a data do local
				$matchDatePlace = explode("•", $matchDatePlace);
				$matchDate = $matchDatePlace[0];
				$matchPlace = $matchDatePlace[1];

				// Pega o nome do primeiro time
				$teams[0]['name'] = $rounds[$c]->item(0)->getElementsByTagName('li')->item($i)->getElementsByTagName('span')->item(3)->nodeValue;

				// Pega o placar do primeiro time
				$teams[0]['score'] = $rounds[$c]->item(0);
				$teams[0]['score'] = $teams[0]['score']->getElementsByTagName('li')->item($i);
				$teams[0]['score'] = $teams[0]['score']->getElementsByTagName('span')->item(5)->nodeValue;

				// Pega o nome do segundo time
				$teams[1]['name'] = $rounds[$c]->item(0);
				$teams[1]['name'] = $teams[1]['name']->getElementsByTagName('li')->item($i);
				$teams[1]['name'] = $teams[1]['name']->getElementsByTagName('span')->item(7);
				$teams[1]['name'] = $teams[1]['name']->getElementsByTagName('a')->item(2)->nodeValue;
				
				// Pega o placar do segundo time
				$teams[1]['score'] = $rounds[$c]->item(0);
				$teams[1]['score'] = $teams[1]['score']->getElementsByTagName('li')->item($i);
				$teams[1]['score'] = $teams[1]['score']->getElementsByTagName('span')->item(6)->nodeValue;

				// Adiciona a partida ao array de dados
				$matches[$c][$i] = array(
					str_replace("\n", '', $teams[0]['name']) => str_replace("\n", '',$teams[0]['score']),
					str_replace("\n", '', $teams[1]['name']) => str_replace("\n", '',$teams[1]['score']),
					'date' => str_replace("\n", '',$matchDate),
					'place' => substr(str_replace("\n", '', $matchPlace), 1)
				);
			}
		}

		// Retorna o array com as partidas
		return $matches;
	}

	/** 
	 * Recupera a tabela do Campeonato Paulista.
	 *
	 * @return Array $groups
	 */
	public function getPLtable()
	{
		// Recupera as página com as tabelas
		$pageContents = $this->getPageContents('https://www.gazetaesportiva.com/campeonatos/paulista/');

		// Retorna apenas o codigo dos grupos
		$tablesCode = substr($pageContents, strpos($pageContents, '<div class="tabela-ge">'));
		$tablesCode = substr($tablesCode, strpos($tablesCode, '<div class="col-lg-8" style="float: left;">'));
		$tablesCode = substr($tablesCode, strpos($tablesCode, '<tbody>'));

		// Arrays para guardar a tabela da fase de grupos
		$groups = array(
			"grupo-a" => [
				0 => [],
				1 => [],
				2 => [],
				3 => []
			],
			"grupo-b" => [
				0 => [],
				1 => [],
				2 => [],
				3 => []
			],
			"grupo-c" => [
				0 => [],
				1 => [],
				2 => [],
				3 => []
			],
			"grupo-d" => [
				0 => [],
				1 => [],
				2 => [],
				3 => []
			] 
		);
		
		// Loop em $groups
		foreach ($groups as $group => $time) {
			// Controlador de loop
			$i = 0;

			// Loop while
			while ($i < 4) {
				// Guarda o logo do time
				$img = substr($tablesCode, strpos($tablesCode, '<img'));
				$img = str_replace(substr($img, strpos($img, '>')+ 1), '', $tablesCode);

				// Remove o logo do time
				$tablesCode = str_replace($img, '', $tablesCode);

				// Pega o nome do time
				$name = substr($tablesCode, strpos($tablesCode, '<a class'));
				$nameTAG = str_replace(substr($name, strpos($name, '</a>')), '', $name);
				$name = substr($nameTAG, strpos($nameTAG, '>')+1);

				// Remove o nome do time
				$tablesCode = str_replace($nameTAG, '<table>', $tablesCode);

				// Pega os dados desse time
				$info = substr($tablesCode, strpos($tablesCode, '<table>'));

				// Inicia um documento DOM
				$doc = new DOMDocument();
				$doc->loadHTML($info);

				// Pega o conteudo HTML do documento
				$xpath = new DOMXPath($doc);

				// Pega os campos da tabela
				$table = $xpath->query("//table");
				$table = $table->item(0)->nodeValue;

				// Separa os dados do time
				$data = explode(" ", $table);
				$data = explode("\n", $data[0]);
				
				// Insere os dados no array de grupos
				$groups[$group][$i] = array(
					"name" => $name,
					"pg" => $data[1],
					"j" => $data[2],
					"v" => $data[3],
					"e" => $data[4],
					"d" => $data[5],
					"gp" => $data[6],
					"gc" => $data[7],
					"sg" => $data[8]
				);

				// Remove o time atual da tabela
				$table = substr($tablesCode, strpos($tablesCode, '<tr'));
				
				// Atualiza a tabela da página
				$tablesCode = $table;

				// Incrementa o controlador de loop
				$i++;
			}
		}

		// Retorna a tabela
		return $groups;
	}

	/**
	 * Recupera os jogos das rodadas do Campeonato Paulista.
	 *
	 * @return Array $matches
	 */
	public function getPLmatches()
	{
		// Recupera as página com as tabelas
		$pageContents = $this->getPageContents('https://www.gazetaesportiva.com/campeonatos/paulista/');

		// Inicia um documento DOM
		$doc = new DOMDocument();
		$doc->loadHTML($pageContents);

		// Pega o conteudo HTML do documento
		$xpath = new DOMXPath($doc);

		// Guarda o código fonte das rodadas
		$rounds = array();

		// Guarda os dados das partidas
		$matches = array();

		// Loop for (1 a 12)
		for ($a=1; $a <= 12; $a++) {
			// Separa as rodadas
			$rounds[$a] = $xpath->query("//div[contains(@class,'rodadas_grupo__numero_rodada_{$a}')]");
		}

		// Loop for (1 a 12)
		for ($c=1; $c <= 12; $c++) {
			// Loop for (0 a 8)
			for ($i=0; $i < 8; $i++) {
				// Pega a data e o lugar da partida
				$matchDatePlace = $rounds[$c]->item(0);
				$matchDatePlace = $matchDatePlace->getElementsByTagName('li')->item($i);
				$matchDatePlace = $matchDatePlace->getElementsByTagName('span')->item(0)->nodeValue;

				// Separa a data do local
				$matchDatePlace = explode("•", $matchDatePlace);
				$matchDate = $matchDatePlace[0];
				$matchPlace = $matchDatePlace[1];

				// Pega o nome do primeiro time
				$teams[0]['name'] = $rounds[$c]->item(0)->getElementsByTagName('li')->item($i)->getElementsByTagName('span')->item(3)->nodeValue;

				// Pega o placar do primeiro time
				$teams[0]['score'] = $rounds[$c]->item(0);
				$teams[0]['score'] = $teams[0]['score']->getElementsByTagName('li')->item($i);
				$teams[0]['score'] = $teams[0]['score']->getElementsByTagName('span')->item(5)->nodeValue;

				// Pega o nome do segundo time
				$teams[1]['name'] = $rounds[$c]->item(0);
				$teams[1]['name'] = $teams[1]['name']->getElementsByTagName('li')->item($i);
				$teams[1]['name'] = $teams[1]['name']->getElementsByTagName('span')->item(7);
				$teams[1]['name'] = $teams[1]['name']->getElementsByTagName('a')->item(2)->nodeValue;
				
				// Pega o placar do segundo time
				$teams[1]['score'] = $rounds[$c]->item(0);
				$teams[1]['score'] = $teams[1]['score']->getElementsByTagName('li')->item($i);
				$teams[1]['score'] = $teams[1]['score']->getElementsByTagName('span')->item(6)->nodeValue;

				// Adiciona a partida ao array de dados
				$matches[$c][$i] = array(
					str_replace("\n", '', $teams[0]['name']) => str_replace("\n", '',$teams[0]['score']),
					str_replace("\n", '', $teams[1]['name']) => str_replace("\n", '',$teams[1]['score']),
					'date' => str_replace("\n", '',$matchDate),
					'place' => substr(str_replace("\n", '', $matchPlace), 1)
				);
			}
		}

		// Retorna o array com as partidas
		return $matches;
	}

	/**
	 * Recupera os jogos das rodadas da Libertadores da América.
	 *
	 * @return Array $matches
	 */
	public function getLBmatches()
	{
		// Recupera as página com as tabelas
		$pageContents = $this->getPageContents('https://gauchazh.clicrbs.com.br/esportes/tabelas/libertadores');

		// Inicia um documento DOM
		$doc = new DOMDocument();
		$doc->loadHTML($pageContents);

		// Pega o conteudo HTML do documento
		$xpath = new DOMXPath($doc);

		// Guarda os dados das partidas
		$matches = array();
		
		// Guarda as 3 tabelas
		$table = $xpath->query("//ul[contains(@class,'slider-list')]");
		$table = $table->item(0);

		// Guarda o código fonte das rodadas
		$rounds = array(
			1 => $table->getElementsByTagName('li')->item(0),
			2 => $table->getElementsByTagName('li')->item(1),
			3 => $table->getElementsByTagName('li')->item(2)
		);

		$rounds = array(
			1 => $rounds[1]->getElementsByTagName('ul')->item(0),
			2 => $table->getElementsByTagName('ul')->item(1),
			3 => $table->getElementsByTagName('ul')->item(2)
		);

		// Loop em $rounds
		foreach ($rounds as $round => $match) {
			// Define quantas partidas pegar
			if ($round !== 2) $whileCondition = 6;
			else  $whileCondition = 16;

			// Controlador de loop
			$i = 0;

			// Loop while
			while ($i < $whileCondition) {

				// Pega a data e hora da partida
				$currentMatch = $match->getElementsByTagName('li')->item($i);

				$matchDay = $currentMatch->getElementsByTagName('h4')->item(0)->nodeValue;
				$matchHour = $currentMatch->getElementsByTagName('h4')->item(1)->nodeValue;
				$matchDate = $matchDay . ' - ' . $matchHour;

				// Pega o lugar da partida
				$matchPlace = $currentMatch->getElementsByTagName('h4')->item(2)->nodeValue;

				// Pega o nome e placar do primeiro time
				$times[0]['name'] = $currentMatch->getElementsByTagName('h3')->item(0)->nodeValue;
				$times[0]['score'] = $currentMatch->getElementsByTagName('h3')->item(2)->nodeValue;

				// Pega o nome e placar do segundo time
				$times[1]['name'] = $currentMatch->getElementsByTagName('h3')->item(6)->nodeValue;
				$times[1]['score'] = $currentMatch->getElementsByTagName('h3')->item(5)->nodeValue;

				// Adiciona a partida ao array de dados
				$matches[$round][$i] = array(
					$times[0]['name'] => $times[0]['score'],
					$times[1]['name'] => $times[1]['score'],
					'date' => $matchDate,
					'place' => $matchPlace
				);

				// Incrementa o controlador de loop
				$i++;
			}
		}
		
		// Retorna o array com as partidas
		return $matches;
	}
}

?>