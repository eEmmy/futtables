<?php

// Ativa os avisos de erros do PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

// Carrega a classe
require_once 'FutTables.php';

// Instancia um objeto FutTables
$futtables = new FutTables();

// Verifica os parametros passados via GET
if (isset($_GET['campeonato'])) {
	// Verifica qual campeonato deve ser recuperado
	if ($_GET['campeonato'] === 'brasileiro') {
		// Verifica se foi informado qual o tipo de dado a ser retornado
		if (isset($_GET['tipo'])) {
			// Verifica qual o tipo de dado a ser retornado
			if ($_GET['tipo'] === 'tabela') {
				// Armazena os dados da tabela
				$data = $futtables->getBRtable();

				// Retorna os dados encodados em JSON
				echo json_encode($data);
			}
			else if ($_GET['tipo'] === 'rodadas') {
	 			// Armazena os dados das partidas
				$data = $futtables->getBRmatches();

				// Retorna os dados encodados em JSON
				echo json_encode($data);
	 		} 
			else {
				// Retorna um erro no tipo de solicitação
				echo json_encode($futtables->wrongRequest());
			}
			
		} 
		else {
			// Retorna um erro no tipo de solicitação
	 		echo json_encode($futtables->wrongRequest());
		}
	}
	else if ($_GET['campeonato'] === 'paulista') {
	 	// Verifica se foi informado qual o tipo de dado a ser retornado
	 	if (isset($_GET['tipo'])) {
	 		// Verifica qual o tipo de dado a ser retornado
	 		if ($_GET['tipo'] === 'tabela') {
	 			// Armazena os dados da tabela
				$data = $futtables->getPLtable();

				// Retorna os dados encodados em JSON
				echo json_encode($data);
	 		}
	 		else if ($_GET['tipo'] === 'rodadas') {
	 			// Armazena os dados das partidas
				$data = $futtables->getPLmatches();

				// Retorna os dados encodados em JSON
				echo json_encode($data);
	 		} 
	 		else {
	 			// Retorna um erro no tipo de solicitação
	 			echo json_encode($futtables->wrongRequest());
	 		}
	 		
	 	} 
	 	else {
	 		// Retorna um erro no tipo de solicitação
			echo json_encode($futtables->wrongRequest());
	 	}
	}
	else if ($_GET['campeonato'] === 'libertadores') {
		// Verifica se foi informado qual o tipo de dado a ser retornado
		if (isset($_GET['tipo'])) {
			// Verifica qual o tipo de dado a ser retornado
			if ($_GET['tipo'] === 'rodadas') {
				// Armazena os dados das partidas
				$data = $futtables->getLBmatches();

				// Retorna os dados encodados em JSON
				echo json_encode($data);
			}
			else {
				// Retorna um erro no tipo de solicitação
				echo json_encode($futtables->wrongRequest());
			}
			
		} 
		else {
			// Retorna um erro no tipo de solicitação
			echo json_encode($futtables->wrongRequest());
		}
	}
	else {
		// Retorna um erro no tipo de solicitação
		echo json_encode($futtables->wrongRequest());
	}
}
else {
	// Retorna um erro no tipo de solicitação
	echo json_encode($futtables->wrongRequest());
}

?>