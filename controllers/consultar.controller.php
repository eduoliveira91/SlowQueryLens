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
        $datahoraInicio = DateTime::createFromFormat('Y-m-d H:i:s.u', $datahoraInicio);
        $datahoraFim = DateTime::createFromFormat('Y-m-d H:i:s.u', $datahoraFim);
    
        if ($datahoraInicio && $datahoraFim) {
            return $datahoraInicio <= $datahoraFim;
        }
    
        return false;
    }

    public function processarConsulta() {
        $resultado = [];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'listarLogs') {
            $colunas = $_POST['colunas'] ?? [];
            $datahoraInicio = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['datahoraInicio']);
            $datahoraFim = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['datahoraFim']);
            $filtroSQL = $_POST['filtroSQL'] ?? '';
            $filtroMainTable = $_POST['filtroMainTable'] ?? [];
    
            // Verificar se as datas foram criadas corretamente
            if (!$datahoraInicio || !$datahoraFim) {
                $resultado['error'] = 'Formato de data/hora inválido.';
                echo json_encode($resultado);
                exit;
            }
    
            // Converter as datas para o formato esperado
            $datahoraInicio = $datahoraInicio->format('Y-m-d H:i:s') . '.000000';
            $datahoraFim = $datahoraFim->format('Y-m-d H:i:s') . '.999999';
    
            // Validar o período
            if (!$this->validaPeriodo($datahoraInicio, $datahoraFim)) {
                $resultado['error'] = 'O período informado é inválido.';
                echo json_encode($resultado);
                exit;
            }
    
            // Validar colunas selecionadas
            if (empty($colunas)) {
                $resultado['error'] = 'Nenhuma coluna foi selecionada.';
                echo json_encode($resultado);
                exit;
            }
    
            // Consultar os logs no model
            $dados = ModelsConsultar::ConsultarLogs($colunas, $datahoraInicio, $datahoraFim, $filtroSQL, $filtroMainTable);
    
            if (!empty($dados['dados'])) {
                // Montar tabela
                $tabela = '<table id="logsTable" class="table table-bordered table-striped dt-responsive tables" width="100%">';
                $tabela .= '<thead><tr>';
                foreach ($colunas as $coluna) {
                    $tabela .= '<th>' . htmlspecialchars($coluna) . '</th>';
                }
                $tabela .= '</tr></thead><tbody>';
                foreach ($dados['dados'] as $linha) {
                    $tabela .= '<tr>';
                    foreach ($colunas as $coluna) {
                        $tabela .= '<td>' . htmlspecialchars($linha[$coluna]) . '</td>';
                    }
                    $tabela .= '</tr>';
                }
                $tabela .= '</tbody></table>';
    
                $resultado['table'] = $tabela;

                if (isset($dados['sql'])) {
                    $resultado['sql'] = $dados['sql'];
                }
        
            } else {
                $resultado['error'] = 'Nenhum dado encontrado para os filtros aplicados.';
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




