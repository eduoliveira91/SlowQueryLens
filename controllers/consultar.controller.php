<?php

require_once __DIR__ . "/../models/consultar.models.php";

class ControllerConsultar
{
    public function gerarOpcoesMainTable()
    {
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

    public function listarIndices()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'listarIndices') {
            $dados = ModelsConsultar::RetornaIndices();

            if (isset($dados['error'])) {
                echo json_encode(['error' => $dados['error']]);
                return;
            }

            $indices = [];
            foreach ($dados as $dado) {
                if (!in_array($dado['Key_name'], $indices)) {
                    $indices[] = $dado['Key_name'];
                }
            }

            echo json_encode(['indices' => $indices]);
        } else {
            echo json_encode(['error' => 'Método de requisição inválido.']);
        }
    }


    public function validaPeriodo($datahoraInicio, $datahoraFim)
    {
        $datahoraInicio = DateTime::createFromFormat('Y-m-d H:i:s.u', $datahoraInicio);
        $datahoraFim = DateTime::createFromFormat('Y-m-d H:i:s.u', $datahoraFim);

        if ($datahoraInicio && $datahoraFim) {
            return $datahoraInicio <= $datahoraFim;
        }

        return false;
    }

    public function processarConsulta()
    {
        $resultado = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'listarLogs') {
            $colunas = $_POST['colunas'] ?? [];
            $datahoraInicio = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['datahoraInicio']);
            $datahoraFim = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['datahoraFim']);
            $filtroSQL = $_POST['filtroSQL'] ?? '';
            $filtroMainTable = $_POST['filtroMainTable'] ?? [];

            if (!$datahoraInicio || !$datahoraFim) {
                $this->responderErro('Formato de data/hora inválido.');
                return;
            }

            $datahoraInicio = $datahoraInicio->format('Y-m-d H:i:s') . '.000000';
            $datahoraFim = $datahoraFim->format('Y-m-d H:i:s') . '.999999';

            if (!$this->validaPeriodo($datahoraInicio, $datahoraFim)) {
                $this->responderErro('O período informado é inválido.');
                return;
            }

            if (empty($colunas)) {
                $this->responderErro('Nenhuma coluna foi selecionada.');
                return;
            }

            $dados = ModelsConsultar::ConsultarLogs($colunas, $datahoraInicio, $datahoraFim, $filtroSQL, $filtroMainTable);

            if (!empty($dados['dados'])) {
                $resultado['table'] = $this->montarTabela($dados['dados'], $colunas);
            } else {
                $resultado['error'] = $dados['error'] ?? 'Nenhum registro encontrado.';
            }

            if (isset($dados['sql'])) {
                $resultado['sql'] = $dados['sql'];
            }
        } else {
            $this->responderErro('Método de requisição inválido.');
            return;
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    private function responderErro($mensagem)
    {
        echo json_encode(['error' => $mensagem]);
        exit;
    }

    private function montarTabela($dados, $colunas)
    {
        $tabela = '<table id="logsTable" class="table table-bordered table-striped dt-responsive tables" width="100%">';
        $tabela .= '<thead><tr>';
        foreach ($colunas as $coluna) {
            $tabela .= '<th>' . htmlspecialchars($coluna) . '</th>';
        }
        $tabela .= '</tr></thead><tbody>';
        foreach ($dados as $linha) {
            $tabela .= '<tr>';
            foreach ($colunas as $coluna) {
                if (in_array($coluna, ['time', 'start', 'end'])) {
                    $tabela .= '<td>' . $this->formatarData($linha[$coluna]) . '</td>';
                } else {
                    $tabela .= '<td>' . htmlspecialchars($linha[$coluna]) . '</td>';
                }
            }
            $tabela .= '</tr>';
        }
        $tabela .= '</tbody></table>';

        return $tabela;
    }

    private function formatarData($data)
    {
        $dataHora = DateTime::createFromFormat('Y-m-d H:i:s.u', $data);
        if ($dataHora) {
            $formattedDate = $dataHora->format('d/m/Y H:i:s');
            $microseconds = $dataHora->format('u');
            return $microseconds != '000000' ? $formattedDate . '.' . $microseconds : $formattedDate;
        }
        return htmlspecialchars($data);
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'listarLogs') {
        $controller = new ControllerConsultar();
        $controller->processarConsulta();
    } elseif ($action === 'listarIndices') {
        $controller = new ControllerConsultar();
        $controller->listarIndices();
    }
}



