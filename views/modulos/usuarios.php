<?php
require_once "models/usuarios.models.php";
?>
<div class="content-wrapper">
   
  <section class="content-header">
  <h1>
    Administrar Usuários
  </h1>
  <ol class="breadcrumb">
    <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
    <li class="active">Administrar Usuários</li>
  </ol>
  </section>

  <section class="content">
 
  <div class="box">

    <div class="box-header with-border">
      <button class="btn btn-primary" data-toggle="modal" data-target="#modalInserirUsuario">
      Inserir Usuário
      </button>
    </div>

    <div class="box-body">
    <table class="table table-bordered table-striped dt-responsive tables" width="100%">
      <thead>
      <tr>
        <th style="width:10px">Cód</th>
        <th>Nome</th>
        <th>Usuário</th>
        <th>Foto</th>
        <th>Perfil</th>
        <th>Estado</th>
        <th>Útimo Login</th>
        <th>Ações</th>
      </tr>
      </thead>
      <tbody>
      <?php
        $usuarios = ModelsUsuarios::ListaUsuarios();

        foreach ($usuarios as $usuario) {
          if (is_array($usuario)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($usuario["id"]) . '</td>';
            echo '<td>' . htmlspecialchars($usuario["nome"]) . '</td>';
            echo '<td>' . htmlspecialchars($usuario["usuario"]) . '</td>';
            echo '<td><img src="' . htmlspecialchars($usuario["foto"]) . '" class="img-thumbnail" width="25px"></td>';
            echo '<td>' . htmlspecialchars($usuario["perfil"]) . '</td>';
            echo '<td>' . ($usuario["estado"] == 1 ? '<button class="btn btn-success btn-xs">Ativado</button>' : '<button class="btn btn-danger btn-xs">Desativado</button>') . '</td>';
            echo '<td>' . htmlspecialchars($usuario["ultimo_login"]) . '</td>';
            echo '<td>
              <div>
                <button class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></button>
                <button class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button>
              </div>
            </td>';
            echo '</tr>';
          }
        }
      ?>
      </tbody>
    </table>
    </div>

  </div>
  </section>
</div>
<!--===========================================
Modal Inserir Usuário
============================================-->
<div id="modalInserirUsuario" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
    <form role="form" method="post" enctype="multipart/form-data">
    
    <!--=====================================
    Cabeçario Modal
    ======================================-->
    <div class="modal-header" style="background:#3c8dbc; color:white">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Inserir Usuário</h4>
    </div>
    
    <!--=====================================
    Corpo Modal
    ======================================-->
    <div class="modal-body">
      <!--Input Nome-->
      <div class="box-body">
      <div class="form-group">
        <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-user"></i></span>
        <input type="text" class="form-control input-lg" name="novoNome" placeholder="Inserir Nome" required>
        </div>
      </div>
      </div>
      <!--Input Usuário-->
      <div class="box-body">
      <div class="form-group">
        <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-key"></i></span>
        <input type="text" class="form-control input-lg" name="novoUsuario" placeholder="Inserir Nome Usuário" required>
        </div>
      </div>
      </div>
      <!--Input Senha-->
      <div class="box-body">
      <div class="form-group">
        <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
        <input type="password" class="form-control input-lg" name="novoPassword" placeholder="Inserir Senha" required>
        </div>
      </div>
      </div>
      <!--Input Perfil-->
      <div class="box-body">
      <div class="form-group">
        <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-user"></i></span>
        <select class="form-control input-lg" name="novoPerfil">
          <option value="">Selecione Perfil</option>
          <option value="Administrador">Administrador</option>
          <option value="Administrador_Produtos">Administrador Produtos</option>
          <option value="Vendedor">Vendedor</option>
        </select>
        </div>
      </div>
      </div>
      <!--Inserir Foto-->
      <div class="form-group">
      <div class="panel">INSERIR FOTO</div>
      <input type="file" class="novaFoto" name="editarFoto">
      <p class="help-block">Tamanho máximo da foto 2MB</p>
      <img src="views/img/usuarios/default/anonymous.png" class="img-thumbnail" width="100px">
      <input type="hidden" name="fotoAtual" id="fotoAtual">
      </div>

    </div>
    
    <!--=====================================
    Roda Pé Modal
    ======================================-->
    <div class="modal-footer">
      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Sair</button>
      <button type="submit" class="btn btn-primary">Salvar</button>
    </div>

    <?php
      $criarUsuario = new ControllerUsuarios();
      $criarUsuario -> CriarUsuario();
    ?>
    </form>
  </div>
  </div>
</div>
