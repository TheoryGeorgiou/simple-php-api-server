<?php

$servername = "localhost";
$username = "";
$password = "";
$database = "";


// Create connection
$con = mysqli_connect($servername, $username, $password, $database);
//echo mysqli_connect_error();

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

//get number of rows in a query.
function mysqli_count($table,$where){
    global $con;

    $query = "SELECT COUNT(*) as row_count FROM $table WHERE $where";
    $result = $con->query($query);
    if ($con->query($query) != TRUE) { 
		echo "Mysql Error: ".mysqli_error($con);
		return false;
    }else{
        $get_count = $result->fetch_assoc();
        return $get_count['row_count'];
    }
}

//insert mysql, returns id of entry.
function mysqli_insert($table,$insert){ 
global $con;

	$field_str = '';
	$value_str = '';
	$insert['create_date'] = time();
	$insert['update_date'] = time();

	//santize insert data
	foreach($insert as $key=>$value){
		$insert[$key] = mysqli_real_escape_string($con,trim($value));
		$field_str .= ','.$key;
		$value_str .= ',\''.$insert[$key].'\'';
	}

	$field_str = ltrim($field_str, ',');
	$value_str = ltrim($value_str, ',');
	$sql = "INSERT INTO $table ($field_str) VALUES ($value_str)";
	if ($con->query($sql) == TRUE) {
		return mysqli_insert_id($con);
	} else {
		echo "Mysql Error: ".mysqli_error($con);
		return false;
	}
}


//update entry.
function mysqli_update($table,$insert,$where){ 
global $con;

	$field_str = '';
	$value_str = '';
	$insert['update_date'] = time();

    $update_str = '';
    foreach($insert as $key=>$value){
    $update_str .= ','.$key." = '".mysqli_real_escape_string($con,$value)."'";}
    $update_str = ltrim($update_str,',');
    
    $rec = false;
    $sql = "UPDATE $table SET $update_str WHERE $where ";
    $try = $con->query($sql);
    if ($try === TRUE && mysqli_affected_rows($con) >= 0) {
        return true;
    }else{
		echo "Mysql Error: ".mysqli_error($con);
		return false;
	}
}

//will update or insert if it does not exist, specify last param as true to get more info, otherwise will
//return true or false; always retruns false if failed.
function mysqli_upsert($table,$insert,$where,$info = false){
    $return = false;
    if (mysqli_count($table,$where) > 0){
        $return = mysqli_update($table,$insert,$where);
        if ($info){
            $return_array['type'] = 'update';
            $return_array['successful'] = true;
        }
    }else{
        $id = mysqli_insert($table,$insert);
        if ($id >= 0){
            $return = true;
            if ($info){
                $return_array['type'] = 'insert';
                $return_array['successful'] = true;
                $return_array['id'] = $id;
            }
        }
    }
    
    if (isset($return_array)){
        return $return_array;
    }
    return $return;
}

//select rows.
function mysqli_select($query){
    global $con;
    global $row_total;
    $row_total = 0;

    $result = $con->query($query);
    if ($con->query($query) != TRUE) { 
		echo "Mysql Error: ".mysqli_error($con);
		return false;
    }else{
        $i = 0;
        $return = array();
        while ($row = $result->fetch_assoc()){
            $return[$i] = $row;
            $i+=1;
        }
        return $return;
    }
}

//select single entry
function mysqli_select_single($query){
    $return = scr_mysqli_select($query.' LIMIT 1');
    if ($return){
        return $return[0];
    }else{
        return false;
    }
}





?>