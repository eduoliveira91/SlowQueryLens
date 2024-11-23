<?php

require_once __DIR__ . "/../models/consultar.models.php";

class ControllerConsultar {
    //uma função para carregar uma lista de checkboxes agrupando a informação do campo main_table da tabela logs.
    public function gerarOpcoesMainTable() {
        $dados = ModelsConsultar::RetornaOpcoesMainTable();

        if (isset($dados['error'])) {
            echo '<p>' . $dados['error'] . '</p>';
        } else {
            echo '<div class="checkbox">';
            echo '<label>';
            echo '<input type="checkbox" data-column="sem_informacao" value="sem_informacao"><b>Sem informação</b>';
            echo '</label>';
            echo '</div>';

            foreach ($dados as $dado) {
                echo '<div class="checkbox">';
                echo '<label>';
                echo '<input type="checkbox" data-column="' . htmlspecialchars($dado['main_table']) . '" value="' . htmlspecialchars($dado['main_table']) . '"> ' . htmlspecialchars($dado['main_table']);
                echo '</label>';
                echo '</div>';
            }

        }
    }

    public function validaPeriodo($datahoraInicio, $datahoraFim) {
        $datahoraInicio = DateTime::createFromFormat('Y-m-d H:i:s', $datahoraInicio);
        $datahoraFim = DateTime::createFromFormat('Y-m-d H:i:s', $datahoraFim);

        return true;
    }

    public function processarConsulta() {
        $resultado = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'listarLogs') {
            $colunas = $_POST['colunas'] ?? [];
            $datahoraInicio = '1901-01-01 23:59:59';
            $datahoraFim = '2199-01-01 23:59:59';
            $filtroSQL = $_POST['filtroSQL'] ?? [];
            $filtroMainTable = $_POST['filtroMainTable'] ?? '';

            if (empty($colunas)) {
                $resultado['error'] = 'Nenhuma coluna foi selecionada.';
            } else {
                if (!validaPeriodo($periodo)) {
                    $resultado['error'] = 'Período inválido.';
                }
                else {
                    $dados = ModelsConsultar::ConsultarLogs($colunas, $datahoraInicio, $datahoraFim, $filtroSQL, $filtroMainTable);

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




