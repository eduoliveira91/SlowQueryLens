<?php

require_once __DIR__ . "/../models/consultar.models.php";

class ControllerConsultar {
    public function processarConsulta() {
        $resultado = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'listarLogs') {
            // Obtém as colunas selecionadas
            $colunas = $_POST['colunas'] ?? [];
            if (empty($colunas)) {
                $resultado['error'] = 'Nenhuma coluna foi selecionada.';
            } else {
                // Chama o model para buscar os dados
                $dados = ModelsConsultar::ConsultarLogs($colunas);

                if (isset($dados['error'])) {
                    $resultado['error'] = $dados['error'];
                } elseif (!empty($dados)) {
                    // Monta a tabela dinamicamente
                    $tabela = '<table id="logsTable" class="table table-bordered table-striped dt-responsive tables" width="100%">';
                    $tabela .= '<thead><tr>';
                    foreach ($colunas as $coluna) {
                        $tabela .= '<th>' . htmlspecialchars($coluna) . '</th>';
                    }
                    $tabela .= '</tr></thead><tbody>';
                    foreach ($dados as $linha) {
                        $tabela .= '<tr>';
                        foreach ($colunas as $coluna) {
                            $tabela .= '<td>' . htmlspecialchars($linha[$coluna]) . '</td>';
                        }
                        $tabela .= '</tr>';
                    }
                    $tabela .= '</tbody></table>';

                    $resultado['table'] = $tabela;
                } else {
                    $resultado['error'] = 'Nenhum dado encontrado na tabela.';
                }
            }
        } else {
            $resultado['error'] = 'Método de requisição inválido.';
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }
}

// Instância do controller e execução
$controller = new ControllerConsultar();
$controller->processarConsulta();




