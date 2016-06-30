<?php






//ENTER THE RELEVANT INFO BELOW
    $mysqlUserName      = "root";
    $mysqlPassword      = "";
    $mysqlHostName      = "localhost";
    $DbName             = "project1";
    $backup_name        = "mybackup.sql";
    $tables             = array("biometricsettings","tbl_company","tbl_department","tbl_gatepass","tbl_gatepass_in","tbl_gatepass_out","tbl_holidays","tbl_in_time","tbl_leave","tbl_login","tbl_osd","tbl_ot_in_time","tbl_ot_list","tbl_ot_out_time","tbl_out_time","tbl_salary_sheet_settings","tbl_shifts","tbl_shift_allotment","tbl_shift_change","tbl_temp","userinfo");

   //or add 5th parameter(array) of specific tables:    array("mytable1","mytable2","mytable3") for multiple tables

    Export_Database($mysqlHostName,$mysqlUserName,$mysqlPassword,$DbName,  $tables=false, $backup_name=false );

    function Export_Database($host,$user,$pass,$name,  $tables=false, $backup_name=false )
    {
        $mysqli = new mysqli($host,$user,$pass,$name); 
        $mysqli->select_db($name); 
        $mysqli->query("SET NAMES 'utf8'");

        $queryTables    = $mysqli->query('SHOW TABLES'); 
        while($row = $queryTables->fetch_row()) 
        { 
            $target_tables[] = $row[0]; 
        }   
        if($tables !== false) 
        { 
            $target_tables = array_intersect( $target_tables, $tables); 
        }
        foreach($target_tables as $table)
        {
            $result         =   $mysqli->query('SELECT * FROM '.$table);  
            $fields_amount  =   $result->field_count;  
            $rows_num=$mysqli->affected_rows;     
            $res            =   $mysqli->query('SHOW CREATE TABLE '.$table); 
            $TableMLine     =   $res->fetch_row();
            $content        = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) 
            {
                while($row = $result->fetch_row())  
                { //when started (and every after 100 command cycle):
                    if ($st_counter%100 == 0 || $st_counter == 0 )  
                    {
                            $content .= "\nINSERT INTO ".$table." VALUES";
                    }
                    $content .= "\n(";
                    for($j=0; $j<$fields_amount; $j++)  
                    { 
                        $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); 
                        if (isset($row[$j]))
                        {
                            $content .= '"'.$row[$j].'"' ; 
                        }
                        else 
                        {   
                            $content .= '""';
                        }     
                        if ($j<($fields_amount-1))
                        {
                                $content.= ',';
                        }      
                    }
                    $content .=")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) 
                    {   
                        $content .= ";";
                    } 
                    else 
                    {
                        $content .= ",";
                    } 
                    $st_counter=$st_counter+1;
                }
            } $content .="\n\n\n";
        }
        //$backup_name = $backup_name ? $backup_name : $name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
        $backup_name = $backup_name ? $backup_name : $name.".sql";

        /*$db1= 1;
        $db2=$db1.".sql";

        header('Content-Type: application/octet-stream');   
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$db2."\"");*/ 

       /* $file_to_delete = './12.sql';
        unlink($file_to_delete);*/

        file_put_contents('./12.sql', $content);


        echo $content; 
    }



    $mysqli = new mysqli("us-cdbr-iron-east-04.cleardb.net", "bfcd4b322c6a83", "beddbea6", "heroku_bf0d9aa88fd9c5b");
    $mysqli->query('SET foreign_key_checks = 0');
    if ($result = $mysqli->query("SHOW TABLES"))
    {
        while($row = $result->fetch_array(MYSQLI_NUM))
        {
            $mysqli->query('DROP TABLE IF EXISTS '.$row[0]);
        }
    }

    $mysqli->query('SET foreign_key_checks = 1');
    $mysqli->close();


    $filename = '12.sql';
    $mysql_host = 'us-cdbr-iron-east-04.cleardb.net';
    $mysql_username = 'bfcd4b322c6a83';
    $mysql_password = 'beddbea6';
    $mysql_database = 'heroku_bf0d9aa88fd9c5b';


    mysql_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysql_error());

    mysql_select_db($mysql_database) or die('Error selecting MySQL database: ' . mysql_error());


    $templine = '';
    $lines = file($filename);
    foreach ($lines as $line)
    {
    if (substr($line, 0, 2) == '--' || $line == '')
        continue;

    $templine .= $line;
    if (substr(trim($line), -1, 1) == ';')
    {
        mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
        $templine = '';
    }
    }
     echo "Tables imported successfully";


?>


<script>
setTimeout(function () { window.location.reload(); }, 60*60*1000);
// just show current time stamp to see time of last refresh.
document.write(new Date());
</script>