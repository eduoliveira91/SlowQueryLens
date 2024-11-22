<?php

require_once __DIR__ . "/../models/consultar.models.php";

class ControllerConsultar {
    //uma função para carregar uma lista de checkboxes agrupando a informação do campo main_table da tabela logs.
    public function gerarOpcoesMainTable() {
        $dados = ModelsConsultar::RetornaOpcoesMainTable();

        if (isset($dados['error'])) {
            echo '<p>' . $dados['error'] . '</p>';
        } else {
            foreach ($dados as $dado) {
                echo '<div class="checkbox">';
                echo '<label>';
                echo '<input type="checkbox" name="colunas[]" value="' . htmlspecialchars($dado['main_table']) . '"> ' . htmlspecialchars($dado['main_table']);
                echo '</label>';
                echo '</div>';
            }
        }
    }

    public function processarConsulta() {
        $resultado = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'listarLogs') {
            $colunas = $_POST['colunas'] ?? [];
            if (empty($colunas)) {
                $resultado['error'] = 'Nenhuma coluna foi selecionada.';
            } else {
                $dados = ModelsConsultar::ConsultarLogs($colunas);

                if (isset($dados['error'])) {
                    $resultado['error'] = $dados['error'];
                } elseif (!empty($dados)) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ControllerConsultar();
    $controller->processarConsulta();
}




