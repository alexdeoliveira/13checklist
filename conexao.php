<?php
/**
 * @author Walter Cassiano waltercassiano@gmail.com
 * @access public
 */
class Conexao extends SQLite3 {
  /**
   * Nome do Site
   * @var  String 
   */
  private $SiteNome = 'teste';
  /**
   * Dados a serem salvos
   * @var Array 
   * 
   */
  private $arrayCampos = array();
  
  /**
   * Query Sql
   * @var String 
   */
  private $sql = '';

  
  public function __construct() {
    
    $this->open(dirname(__FILE__) . DIRECTORY_SEPARATOR ."database/checklist.sqlite", SQLITE3_OPEN_READWRITE);
    
     if( filter_input_array(INPUT_POST)){
       $this->insert($_POST);
     }  
     if(filter_input_array(INPUT_GET) && filter_input(INPUT_GET, 'dados')){
      $this->requestJson();
     }
     if(filter_input_array(INPUT_GET) && filter_input(INPUT_GET, 'reset')){
      $this->reset();
     }
     $this->close();
     
  }
  /**
   * Recebe a serem salvos no banco. Os serão serializados.
   * @access private
   * @param type $dados Array de chave e valor. Está sendo usado name (html atributo) e value (html atributo)
   * @return Void 
   */
  private function insert($dados = array()){
    $this->dados = serialize($dados);
    $this->sql = 'DELETE FROM checklist WHERE id=0';
    $this->query($this->sql);
    $this->sql = "INSERT INTO checklist (id,site,site_value) VALUES (0, '". $this->escapeString( $this->SiteNome ). "', '". $this->escapeString( $this->dados )."' ) ";
    $this->query($this->sql);
  }
  
  /**
   * Apaga todos dos registros do formulário
   * @access private
   * @return Void 
   */
  private function reset(){
     $this->sql = 'DELETE FROM checklist WHERE id=0';
    $this->query($this->sql);
  }

  /**
   * Seleciona todos os registros do formulário
   * @access private
   * @return Array Resultado da consulta SQL
   */
  public function select(){
    $this->sql = 'SELECT * FROM checklist where id=0';
   return $this->query($this->sql)->fetchArray(SQLITE3_ASSOC);
  }
  
  /**
   * Recupera campos do formulários e o converte em JSON
   * @access private
   * @return String Um string em formato Json
   */
  public function requestJson(){
    $dados = $this->select();
     echo json_encode(unserialize($dados['site_value']));
  }

  
  
  
}

if(filter_input_array(INPUT_GET) || filter_input_array(INPUT_POST)){
  $conexao = new Conexao();
  
}else{
   header("Location: index.html");
   die();
}
?>