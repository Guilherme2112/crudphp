<?php

mysqli_report(MYSQLI_REPORT_STRICT);
function open_database() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        return $conn;
    } catch (Exception $e) {
        echo $e->getMessage();
        return null;
    }
}
function close_database($conn)
{
    try {
        mysqli_close($conn);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
/**
 *  Pesquisa um Registro pelo ID em uma Tabela
 */
function find( $table = null, $id = null ) {

    $database = open_database();
    $found = null;
    try {
        if ($id) {
            $sql = "SELECT * FROM " . $table . " WHERE id = " . $id;
            $result = $database->query($sql);

            if ($result->num_rows > 0) {
                $found = $result->fetch_assoc();
            }

        } else {

            $sql = "SELECT * FROM " . $table;
            $result = $database->query($sql);

            if ($result->num_rows > 0) {
                $found = $result->fetch_all(MYSQLI_ASSOC);

                /* Metodo alternativo
                $found = array();
                while ($row = $result->fetch_assoc()) {
                  array_push($found, $row);
                } */
            }
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type'] = 'danger';
    }

    close_database($database);
    return $found;
}
function save($table=null,$data = null)
{
    $database = open_database();
    $columns = null;
    $values = null;

    foreach ($data as $key => $value) {
        $columns .= trim($key, "'") . ",";
        $values .= "'$value',";
    }

    $columns = rtrim($columns, ',');
    $values = rtrim($values, ',');
    $sql = "INSERT INTO " . $table . "($columns)" . " VALUES " . "($values);";

    try{
        $database ->query($sql);

        $_SESSION['message'] = 'Registro cadastrado com sucesso';
        $_SESSION['type'] = 'success';
    }catch (Exception $e){
        $_SESSION['message'] = 'Ocorreu um erro durante a inserção';
        $_SESSION['type'] = 'danger';
    }
    close_database($database);
}
function update($table =null,$id=0,$data = null){
    $database =  open_database();

    $itens = null;

    foreach($data as $key=>$value){
        $itens.= trim($key,"'")."= '$value";
    }

    $itens = rtrim($itens,',');

    $sql = " UPDATE ".$table;
    $sql .= "SET $itens";
    $sql .= "WHERE id =".$id.";";

    try{
        $database->query($sql);
        $_SESSION['message']='Registro alterado com sucesso';
        $_SESSION['type'] =  'success';
    }catch (Exception $e){
        $_SESSION['message'] = 'Nao foi possível fazer a alteração!';
        $_SESSION['type'] = 'danger';
    }
    close_database($database);
}