<?php
include_once('../../Slim/Slim.php');
include_once('../../lib/initialize.php');

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();



$authenticateForRole = function ( $role = 'member' ) {
    return function () use ( $role ) {
        //$user = User::fetchFromDatabaseSomehow();
        //if ( $user->belongsToRole($role) === false ) {
        $user = false;
        if ( $user === false ) {
            $app = \Slim\Slim::getInstance();
            $app->flash('error', 'Login required');
            $app->redirect('/login');
        }
    };
};


$app->get('/foo', $authenticateForRole('admin'), function () {
    //Display admin control panel
});

 
$app->get('/hello/:name', function ($name) use ($database) {

    $category = new Category();
    $category->code = '123';
    $category->save();

    if($database) {
        echo "Hello dfsas, $name " . $database->last_query;
    } else {
        echo "hi, $name " . $database->last_query;
    }
});


$app->get('/hi/:name', 'hi');

function hi($name) {

    /*
    $database = MySQLDatabase::getInstance();

    $category = new Category();
    $category->code = '123';
    $category->save();

    if(isset($database)) {
        echo "Hello dfsas, $name " . $database->last_uid;
    } else {
        echo "hi, $name " . $database->last_query;
    }
    */

    echo $name;
}



$app->post('/AuthUserLogin',  'authUserLogin');





// return a view of table data from a view table
$app->get('/t/:table', 'getTables2');
$app->get('/t/:table/:id',  'getTable2');
$app->post('/t/:table', 'addTable');
$app->put('/t/:table/:id',  'updateTable');
$app->delete('/t/:table/:id',  'deleteTable');


//return true table data
$app->get('/s/:table', 'getRealTables');
$app->get('/s/:table/:id',  'getRealTable');
$app->post('/s/:table', 'addRealTable');
$app->put('/s/:table/:id',  'updateRealTable');
$app->delete('/s/:table/:id',  'deleteRealTable');

//return a view table data but dont have a view table
$app->get('/v/:table', 'getRealTables');
$app->get('/v/:table/:id',  'getRealTable');
$app->post('/v/:table', 'addRealTable2');
$app->put('/v/:table/:id',  'updateRealTable');
$app->delete('/v/:table/:id',  'deleteRealTable');




$app->get('/datatables/:table',  'datatables');
$app->get('/datatables/v/:table',  'datatables2');

$app->get('/search/:table', 'searchTable');
$app->get('/search/txn/v/:table/:supplierid', 'searchTxnViewTable');


$app->post('/detail/:table', 'getDetail'); // get the item to put in html table
$app->post('/post/detail/:table', 'postDetail');
$app->put('/post/detail/:table/:table2/:apvhdrid', 'putDetail');


// posting
$app->post('/txn/post/apvhdr/:id', 'postingApvhdr');
$app->get('/txn/posting/apvhdr/:id', 'postingApvhdr');
$app->post('/txn/post/apvhdr/:id/cancelled', 'postingCancelledApvhdr');

$app->get('/posted/detail/:table/:fld', 'getPostedDetail');

$app->get('/txn/:child/:parent/:id', 'getChildTable');
$app->get('/txn/delete/:child/:parent/:id', 'deleteChildTable');
$app->delete('/txn/:child/:parent/:id', 'deleteChildTable');  

$app->get('/fbid/:table/:field/:fieldid', 'findAllByFieldId');

$app->get('/cv-sched', 'getCVSched');
$app->get('/report/cv', 'getReportCV');
$app->get('/report/bank/total', 'getReportBankTotal');

$app->get('/report/chk-day', 'getChkDay');

$app->get('/report/bank/status/:status', 'getReportBankByStatus');

$app->get('/report/cv-bank/:bankid/total', 'getCVBankTotal');
$app->get('/report/cv-bank/:bankid/status/:status', 'getCVBankByStatus');

$app->get('/report/ap-vs-cv', 'getApVsCv');
$app->get('/report/cvhdr-supplier', 'getCvhdrSupplier');
$app->get('/report/apvhdr-account', 'getApvhdrAccount');



 /*****************************  Run App **************************/


$app->get('/r/apvdue', 'apvGetDue');

$app->run();


// ctrl k + ctrl 1 = fold 1st level

function getApVsCv(){
    $app = \Slim\Slim::getInstance();
    $r = $app->request();
    global $database;
    $fr = $database->escape_value($r->get('fr'));
    $to = $database->escape_value($r->get('to'));
    $range = new DateRange($fr,$to);

    echo 'Days,AP Unposted,AP Posted,AP Total,Check Unposted,Check Posted,Check Total';
    echo PHP_EOL;

    $begin = new DateTime($range->fr);
    $end = new DateTime($range->to);
    $end = $end->modify('+1 day'); 
    
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);
    
    
    foreach($daterange as $date){
        $currdate = $date->format("Y-m-d");


        $tot = 0;
        echo $currdate.',';
        for($ctr=0; $ctr <= 1; $ctr++) {
            //echo $ctr.',';

            $sql = "SELECT SUM(totamount) AS totamount FROM vapvhdr ";
            $sql .= "WHERE due = '".$currdate."' AND posted = ". $ctr;
            $vapvhdr = vApvhdr::find_by_sql($sql); 
            $vapvhdr = array_shift($vapvhdr);
            echo empty($vapvhdr->totamount) ? '0.00': $vapvhdr->totamount;
            //echo $ctr==1 ? '':',';
            $tot = $tot + $vapvhdr->totamount;
            echo $ctr==1 ? ','.$tot : ',';
            //echo ',';
        }
        echo ',';



        $tot2 = 0;
        for($ctr=0; $ctr <= 1; $ctr++) {
            //echo $ctr.',';

            $sql = "SELECT SUM(amount) AS amount FROM vcvchkdtl ";
            $sql .= "WHERE checkdate = '".$currdate."' AND posted = ". $ctr;
            $vcvchkdtl = vCvchkdtl::find_by_sql($sql); 
            $vcvchkdtl = array_shift($vcvchkdtl);
            echo empty($vcvchkdtl->amount) ? '0.00': $vcvchkdtl->amount;
            //echo $ctr==1 ? '':',';
            $tot2 = $tot2 + $vcvchkdtl->amount;
            echo $ctr==1 ? ','.$tot2 : ',';
            //echo ',';
        }
        //echo '0.00';

        echo PHP_EOL;
    }

}


function getCVSched(){
    global $database;
    $app = \Slim\Slim::getInstance();
    $r = $app->request();

    
    $fr =  $database->escape_value($r->get('fr'));
    $to =  $database->escape_value($r->get('to'));


    if(!empty($to) && !empty($fr)){
        
        if(strtotime($to) >= strtotime($fr)){
            //return 'correct range';
        } else {
            return 'invalid range';
        }   
    } else {
        $query_date = 'now';
        // First day of the month.
        $fr = date('Y-m-01', strtotime($query_date));
        // Last day of the month.
        $to = date('Y-m-t', strtotime('now'));
        
        // Minus 15 days from now
        //$fr = date('Y-m-d', strtotime('-14 day'));
        //$to = date('Y-m-d', strtotime('now'));
    }


    $banks = Bank::find_all();

    //$d = array('EWB', 'BPI');
    $d = array();
    echo 'Days,';
    foreach ($banks as $bank) {
       array_push($d, $bank->code);   
    }
    //print_r($d);
    echo join(',', $d);
    echo PHP_EOL;

   

    $begin = new DateTime($fr);
    $end = new DateTime($to);
    $end = $end->modify('+1 day'); 
    
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);
    
    $tot = 0;
    foreach($daterange as $date){
        $currdate = $date->format("Y-m-d");

        echo $currdate.',';
        foreach ($banks as $bank) {
            $sql = "SELECT SUM(amount) as amount FROM cvchkdtl ";
            $sql .= "WHERE bankacctid = '".$bank->id."' ";
            $sql .= "AND checkdate = '".$currdate."' ";
            //echo $sql. PHP_EOL;
            $cvchkdtl = Cvchkdtl::find_by_sql($sql); 
            $cvchkdtl = array_shift($cvchkdtl);
            echo empty($cvchkdtl->amount) ? '0.00': $cvchkdtl->amount;
            echo end($banks)==$bank ? '':',';
        }

        echo PHP_EOL;

    };

    //echo 'jeff '. PHP_EOL . 'salunga';

    //echo json_encode($bank);

}



function getReportCV(){
    global $database;
    $app = \Slim\Slim::getInstance();
    $r = $app->request();


    $fr = $database->escape_value($r->get('fr'));
    $to = $database->escape_value($r->get('to'));


    if(!empty($to) && !empty($fr)){
        
        if(strtotime($to) >= strtotime($fr)){
            //return 'correct range';
        } else {
            return 'invalid range';
        }   
    } else {
        $query_date = 'now';
        // First day of the month.
        $fr = date('Y-m-01', strtotime($query_date));
        // Last day of the month.
        $to = date('Y-m-t', strtotime('now'));
        
        // Minus 15 days from now
        //$fr = date('Y-m-d', strtotime('-14 day'));
        //$to = date('Y-m-d', strtotime('now'));
    }


   
    echo 'Days,Unposted,Posted,Total';
    echo PHP_EOL;

   

    $begin = new DateTime($fr);
    $end = new DateTime($to);
    $end = $end->modify('+1 day'); 
    
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);
    
    
    foreach($daterange as $date){
        $currdate = $date->format("Y-m-d");

        $tot = 0;
        echo $currdate.',';
        for($ctr=0; $ctr <= 1; $ctr++) {
            //echo $ctr.',';

            $sql = "SELECT SUM(b.amount) AS amount FROM cvhdr a, cvchkdtl b ";
            $sql .= "WHERE a.id = b.cvhdrid  AND checkdate = '".$currdate."' AND a.posted = ". $ctr;
            $cvchkdtl = Cvchkdtl::find_by_sql($sql); 
            $cvchkdtl = array_shift($cvchkdtl);
            echo empty($cvchkdtl->amount) ? '0.00': $cvchkdtl->amount;
            //echo $ctr==1 ? '':',';
            $tot = $tot + $cvchkdtl->amount;
            echo $ctr==1 ? ','.$tot : ',';
            //echo ',';
        }
        //echo '0.00';

        echo PHP_EOL;

    };

    //echo 'jeff '. PHP_EOL . 'salunga';

    //echo json_encode($bank);

}



function getReportBankTotal(){
    
    $app = \Slim\Slim::getInstance();
    $r = $app->request();
    global $database;
    $fr = $database->escape_value($r->get('fr'));
    $to = $database->escape_value($r->get('to'));
    $range = new DateRange($fr,$to,false);

    echo 'Days,Total';
    echo PHP_EOL;

    $begin = new DateTime($range->fr);
    $end = new DateTime($range->to);
    $end = $end->modify('+1 day'); 
    
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);
    
    
    foreach($daterange as $date){
        $currdate = $date->format("Y-m-d");
        echo $currdate.',';

        //echo ','.rand(1,100);

        $sql = "SELECT SUM(amount) as amount FROM cvchkdtl ";
        $sql .= "WHERE checkdate = '".$currdate."' ";
        //echo $sql. PHP_EOL;
        $cvchkdtl = Cvchkdtl::find_by_sql($sql); 
        $cvchkdtl = array_shift($cvchkdtl);
        echo empty($cvchkdtl->amount) ? '0.00': $cvchkdtl->amount;
        //echo end($banks)==$bank ? '':',';

        echo PHP_EOL;
    }
}



function getCVBankTotal($bankid){
    
    $app = \Slim\Slim::getInstance();
    $r = $app->request();
    global $database;
    $fr = $database->escape_value($r->get('fr'));
    $to = $database->escape_value($r->get('to'));
    $range = new DateRange($fr,$to,false);

    echo 'Days,Total';
    echo PHP_EOL;

    $begin = new DateTime($range->fr);
    $end = new DateTime($range->to);
    $end = $end->modify('+1 day'); 
    
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);
    
    
    foreach($daterange as $date){
        $currdate = $date->format("Y-m-d");
        echo $currdate.',';

        //echo ','.rand(1,100);

        $sql = "SELECT SUM(amount) as amount FROM cvchkdtl ";
        $sql .= "WHERE checkdate = '".$currdate."' ";
        $sql .= "AND bankacctid = '".$bankid."' ";
        //echo $sql. PHP_EOL;
        $cvchkdtl = Cvchkdtl::find_by_sql($sql); 
        $cvchkdtl = array_shift($cvchkdtl);
        echo empty($cvchkdtl->amount) ? '0.00': $cvchkdtl->amount;
        //echo end($banks)==$bank ? '':',';

        echo PHP_EOL;
    }
}


function getCVBankByStatus($bankid, $status){
    //global $database;
   // $fr = $database->escape_value($r->get('fr'));
    //$to = $database->escape_value($r->get('to'));
    if($status=='posted'){
        $s = 1;
    } else if($status=='unposted'){
        $s = 0;
    } else {

    }

    

    $app = \Slim\Slim::getInstance();
    $r = $app->request();

    $range = new DateRange($r->get('fr'),$r->get('to'), false);

    echo 'Days,Total';
    echo PHP_EOL;

    $begin = new DateTime($range->fr);
    $end = new DateTime($range->to);
    $end = $end->modify('+1 day'); 
    
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);
    
    
    foreach($daterange as $date){
        $currdate = $date->format("Y-m-d");
        echo $currdate.',';

        //echo ','.rand(1,100);

        $sql = "SELECT SUM(amount) as amount FROM cvhdr a, cvchkdtl b ";
        $sql .= "WHERE a.id = b.cvhdrid AND a.posted = '".$s."' ";
        $sql .= "AND b.checkdate = '".$currdate."' ";
        $sql .= "AND bankacctid = '".$bankid."'";
        //echo $sql. PHP_EOL;
        $cvchkdtl = Cvchkdtl::find_by_sql($sql); 
        $cvchkdtl = array_shift($cvchkdtl);
        echo empty($cvchkdtl->amount) ? '0.00': $cvchkdtl->amount;
        //echo end($banks)==$bank ? '':',';

        echo PHP_EOL;
    }
}


function getReportBankByStatus($status){
    //global $database;
   // $fr = $database->escape_value($r->get('fr'));
    //$to = $database->escape_value($r->get('to'));
    if($status=='posted'){
        $s = 1;
    } else if($status=='unposted'){
        $s = 0;
    } else {

    }

    

    $app = \Slim\Slim::getInstance();
    $r = $app->request();

    $range = new DateRange($r->get('fr'),$r->get('to'));

    echo 'Days,Total';
    echo PHP_EOL;

    $begin = new DateTime($range->fr);
    $end = new DateTime($range->to);
    $end = $end->modify('+1 day'); 
    
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval ,$end);
    
    
    foreach($daterange as $date){
        $currdate = $date->format("Y-m-d");
        echo $currdate.',';

        //echo ','.rand(1,100);

        $sql = "SELECT SUM(amount) as amount FROM cvhdr a, cvchkdtl b ";
        $sql .= "WHERE a.id = b.cvhdrid AND a.posted = '".$s."' ";
        $sql .= "AND b.checkdate = '".$currdate."' ";
        //echo $sql. PHP_EOL;
        $cvchkdtl = Cvchkdtl::find_by_sql($sql); 
        $cvchkdtl = array_shift($cvchkdtl);
        echo empty($cvchkdtl->amount) ? '0.00': $cvchkdtl->amount;
        //echo end($banks)==$bank ? '':',';

        echo PHP_EOL;
    }
}


function getChkDay(){
    $app = \Slim\Slim::getInstance();
    $r = $app->request();
    global $database;
    $fr = $database->escape_value($r->get('fr'));
    $to = $database->escape_value($r->get('to'));
    $range = new DateRange($fr,$to,false);

    $arr = array();
    $p = $r->get('posted');
    $b = $r->get('bankid');

    echo 'Bank,Amount';
    echo PHP_EOL;
    foreach($range->getDaysInterval() as $date){
        $currdate = $date->format("Y-m-d");
        
        $sql = "SELECT bankcode, SUM(amount) as amount ";
        $sql .= "FROM vcvchkdtl WHERE checkdate = '". $currdate."' ";
        if(isset($p) && ($p==1 || $p==0)){
            $sql .= "AND posted = '".$p."' ";
        }
        if(isset($b) && is_uuid($b)){
            $sql .= "AND bankid = '".$b."' ";
        }
        $sql .= "GROUP BY bankid ORDER BY bankcode";
        $cvchkdtls = vCvchkdtl::find_by_sql($sql); 

        //global $database;
        //echo $database->last_query;
        //echo '<br>';

        foreach ($cvchkdtls as $cvchkdtl) {

            //echo $cvchkdtl->bankcode.','.$cvchkdtl->amount.'<br>';

            if(array_key_exists($cvchkdtl->bankcode, $arr)) {
                //echo $arr[$cvchkdtl->bankcode].'<br>';
                //echo $cvchkdtl->bankcode.' double '.$cvchkdtl->amount.'<br>';
                $arr[$cvchkdtl->bankcode] +=  $cvchkdtl->amount;
            } else {

                $arr[$cvchkdtl->bankcode] =  $cvchkdtl->amount;
                //array_push($arr, $c);   
            }

            //$x = (array) $cvchkdtl;
            //echo var_dump($x);
            
            //echo $cvchkdtl->bankcode.','.$cvchkdtl->amount;
            //echo PHP_EOL;
        }          
    }

    foreach ($arr as $key => $value) {
        echo $key.','.$value;
        echo PHP_EOL;
    };
}


function getCvhdrSupplier(){
    $app = \Slim\Slim::getInstance();
    $r = $app->request();
    global $database;
    $fr = $database->escape_value($r->get('fr'));
    $to = $database->escape_value($r->get('to'));
    $data = $database->escape_value($r->get('data'));
    $posted = $database->escape_value($r->get('posted'));
    $range = new DateRange($fr,$to,false);


    $cvhdrs = vCvhdr::group_by_supplier($fr,$to,$posted);
    //global $database;
    //echo $database->last_query;

    if(!empty($data) && $data=='json') {
        echo json_encode($cvhdrs);
    } else {
        echo 'Suppliercode,Supplier,Amount,Pecentage';
        echo PHP_EOL;
        foreach ($cvhdrs as $cvhdr) {
            echo $cvhdr->suppliercode.',';
            echo $cvhdr->supplier.',';
            echo $cvhdr->totchkamt.',';
            echo $cvhdr->percentage.',';
            echo PHP_EOL;
        }
    }

}


function getApvhdrAccount(){
    $app = \Slim\Slim::getInstance();
    $r = $app->request();
    global $database;
    $fr = $database->escape_value($r->get('fr'));
    $to = $database->escape_value($r->get('to'));
    $data = $database->escape_value($r->get('data'));
    $posted = $database->escape_value($r->get('posted'));
    $range = new DateRange($fr,$to,false);


    $apvhdrs = vApvhdr::group_by_account($fr,$to,$posted);
    //global $database;
    //echo $database->last_query;

    if(!empty($data) && $data=='json') {
        echo json_encode($apvhdrs);
    } else {
        echo 'Accountcode,Account,Amount,Pecentage';
        echo PHP_EOL;
        foreach ($apvhdrs as $apvhdr) {
            echo $apvhdr->accountcode.',';
            echo $apvhdr->account.',';
            echo $apvhdr->totamount.',';
            echo $apvhdr->percentage.',';
            echo PHP_EOL;
        }
    }

}


function getChildTable($child, $parent, $id){

    $cTable = ucfirst($child);

    $oTable = $cTable::find_all_by_field_id($parent, $id);

    echo json_encode($oTable);
}


// delete all details with apvhdrid on Apvdtls Table
function deleteChildTable($child, $parent, $id){

    //$app = \Slim\Slim::getInstance();
    $database = MySQLDatabase::getInstance();
    $sTable = ucfirst($child); // apvdtl
    $sTable2 = ucfirst($parent); // apvhdr

    //$request = $app->request()->getBody();
    //$detail = json_decode($request, true);


    //check 1st if there is an existing table2 (parent table)
    //2nd if there is a record with that id
    $par = $sTable2::find_by_id($id);

    if(isset($par) && $par!=false) {
        
        if(!$sTable::delete_all_by_field_id($parent,$id)){
            
            $respone = array(
                'status' => 'error', 
                'code' => '400',
                'message' => 'details not deleted saved'
            );
        } else {
            $respone = array(
                'status' => 'ok', 
                'code' => '200',
                'message' => 'detail deleted'
            );
        }
    } else {
        $respone = array(
            'status' => 'error', 
            'code' => '404',
            'message' => 'the is no '. $parent .' table found'
        );
    }
    echo json_encode($respone);    
}





function getTables($table) {

    

    $sTable = ucfirst($table);
    
    $oTable = $sTable::find_all();

     

    if($oTable){
        echo json_encode($oTable);
    } else {
        $respone = array(
                "status" => "error",
                "message" => "Record(s) not found!"
        );
        echo json_encode($respone);
    }
}

function getTables2($table) {
    global $database;

    $sTable = ucfirst($table);
    
    $vTable = substr_replace($sTable, 'v', 0, 0);
    $ovTables = $vTable::find_all();

    //echo $database->last_query;


    if($ovTables){
        echo json_encode($ovTables);
    } else {
        $respone = array(
                "status" => "error",
                "message" => "Record(s) not found!"
        );
        echo json_encode($respone);
    }
}


function getTable($table, $id) {

    $sTable = ucfirst($table);
    
    $oTable = $sTable::find_by_id($id);

    if($oTable){
        echo json_encode($oTable);
    } else {
        $respone = array(
                "status" => "error",
                "message" => "Record not found!"
        );
        echo json_encode($respone);
    }
    
}

function getTable2($table, $id) {

    $sTable = ucfirst($table);
    
    $vTable = substr_replace($sTable, 'v', 0, 0);

    $ovTable = $vTable::find_by_id($id);

    if($ovTable){
        echo json_encode($ovTable);
    } else {
        $respone = array(
                "status" => "error",
                "message" => "Record not found!"
        );
        echo json_encode($respone);
    }
    
}

function addTable($table) {

    $app = \Slim\Slim::getInstance();
    $sTable = ucfirst($table);

    $request = $app->request()->getBody();
    $get = json_decode($request, true);

    $oTable = new $sTable();

    foreach ($get as $key => $value) {

        if($key=="id") {
            if(isset($value) &&  $value != NULL) {
                $oTable->$key = $value;
            }
        } else {
            $oTable->$key = $value;
        }
    }   

    $success = false;
    $success = $oTable->save();

    if($success) {

        $vTable = substr_replace($sTable, 'v', 0, 0);
        $ovTable = $vTable::find_by_id($oTable->id);
     
        echo json_encode($ovTable);    

    } else {
       $respone = array(
            'status' => 'error', 
            'code' => '404',
            'message' => 'error on saving '.mysql_error()
        );
       echo json_encode($respone);
    }

    /*
    *  add record to a table but response with view table
    */
    
}

function updateTable($table, $id) {

    $app = \Slim\Slim::getInstance();
    $sTable = ucfirst($table);

    $request = $app->request()->getBody();
    $get = json_decode($request, true);

    $oTable = new $sTable();

    foreach ($get as $key => $value) {

        /*
        if($key=="id") {
            if(isset($value) &&  $value != NULL) {
                $oTable->$key = $value;
            }
        } else {

        */
            $oTable->$key = $value;
        #}
    }   

    #$oTable->id = $id;


    $success = false;
    $success = $oTable->save();
       

    if($success) {
    
        $vTable = substr_replace($sTable, 'v', 0, 0);
        $ovTable = $vTable::find_by_id($id);

        echo json_encode($ovTable);    

    } else {

        if(mysql_error()){
            $respone = array(
                'status' => 'error', 
                'code' => '404',
                'message' => 'error on saving '.mysql_error()
            );
        } else {
            $respone = array(
                'status' => 'error', 
                'code' => '504',
                'message' => 'error on saving. No changes have been made'
            );   
        }
        
        echo json_encode($respone); 
    }    

}

function deleteTable($table, $id) {
   
    $sTable = ucfirst($table);
    
    $oTable = new $sTable();

    $oTable->id = $id;   
    if($oTable->delete()) {
        echo '{"id":"'.$id.'"}';
    } else {

        $respone = array(
            'status' => 'error', 
            'code' => '404',
            'message' => 'error on deleting'
        );
  
        echo json_encode($respone); 

        //echo '{"error":"cannot delete data"}';
    }    
}


/**
* Real table
*/

function getRealTables($table) {

    $sTable = ucfirst($table);
    
    $oTable = $sTable::find_all();

    if($oTable){
        echo json_encode($oTable);
    } else {
        $respone = array(
                "status" => "error",
                "message" => "Record(s) not found!"
        );
        echo json_encode($respone);
    }
}


function getRealTable($table, $id) {

    $sTable = ucfirst($table);
    
    $oTable = $sTable::find_by_id($id);

    if($oTable){
        echo json_encode($oTable);
    } else {
        $respone = array(
                "status" => "error",
                "message" => "Record not found!"
        );
        echo json_encode($respone);
    }
    
}



function addRealTable($table) {

    $app = \Slim\Slim::getInstance();
    $sTable = ucfirst($table);

    $request = $app->request()->getBody();
    $get = json_decode($request, true);

    $oTable = new $sTable();

    foreach ($get as $key => $value) {

        if($key=="id") {
            if(isset($value) &&  $value != NULL) {
                $oTable->$key = $value;
            }
        } else {
            $oTable->$key = $value;
        }
    }   

    $success = false;
    $success = $oTable->save();

    if($success) {
     
        echo json_encode($oTable);    

    } else {

        $respone = array(
            'status' => 'error', 
            'code' => '404',
            'message' => 'error on saving '.mysql_error()
        );
       echo json_encode($respone);
    }
    
}

function updateRealTable($table, $id) {
    global $database;

    $app = \Slim\Slim::getInstance();
    $sTable = ucfirst($table);

    $request = $app->request()->getBody();
    $get = json_decode($request, true);

    $oTable = new $sTable();

    foreach ($get as $key => $value) {

        /*
        if($key=="id") {
            if(isset($value) &&  $value != NULL) {
                $oTable->$key = $value;
            }
        } else {

        */
            $oTable->$key = $value;
        #}
    }   

    #$oTable->id = $id;

    if($oTable::find_by_id($id)){
        $success = false;
        $success = $oTable->save();
    } else {
        $success = false;
        $success = $oTable->create();
    }

    
       
    //echo $database->last_query;
    if($success) {
    

        echo json_encode($oTable);    

    } else {
        if(mysql_error()){
            //echo '{"error":" error on saving '.mysql_error().'"}';

            $respone = array(
                'status' => 'error', 
                'code' => '404',
                'message' => 'error on saving '.mysql_error()
            );
            echo json_encode($respone);
        #echo json_encode($success); 
        } else {
            //echo '{"warning":" Nothing to update"}';
            #echo json_encode($success); 

            $respone = array(
                'status' => 'warning', 
                'code' => '504',
                'message' => 'Nothing to update '.mysql_error()
            );
            echo json_encode($respone);
        }

        
    }    

}

function deleteRealTable($table, $id) {
   
    $sTable = ucfirst($table);
    
    $oTable = new $sTable();

    $oTable->id = $id;   
    if($oTable->delete()) {
        echo '{"id":"'.$id.'"}';
    } else {

        $respone = array(
            'status' => 'error', 
            'code' => '404',
            'message' => 'error on deleting'
        );
  
        echo json_encode($respone); 

        //echo '{"error":"cannot delete data"}';
    }    
}






function addRealTable2($table) {

    $app = \Slim\Slim::getInstance();
    $sTable = ucfirst($table);

    $request = $app->request()->getBody();
    $get = json_decode($request, true);

    $oTable = new $sTable();

    foreach ($get as $key => $value) {

        if($key=="id") {
            if(isset($value) &&  $value != NULL) {
                $oTable->$key = $value;
            }
        } else {
            $oTable->$key = $value;
        }
    }   

    $success = false;
    $success = $oTable->save();

    if($success) {

        $vTable = substr_replace($sTable, 'x', 0, 0);
        $ovTable = $vTable::find_by_id($oTable->id);
     
        echo json_encode($ovTable);    

    } else {
       $respone = array(
            'status' => 'error', 
            'code' => '404',
            'message' => 'error on saving '.mysql_error()
        );
       echo json_encode($respone);
    }

    /*
    *  add record to a table but response with view table
    */
    
}







/**
* Datatables API
*/


function datatables($table) {
    
    $database = new MySQLDatabase();
    $app = \Slim\Slim::getInstance();

    $sql = "DESCRIBE ". $table;
    $rows = $database->query($sql);

    $aColumns = array();

    while($row = $database->fetch_row($rows)) {
        $aColumns[] = $row[0];
    }

    #print_r($aColumns);

   // $key = array_search('id',  $aColumns); 
    #echo "<br>".$key."<br>";  

    //unset($aColumns[$key]);

    #print_r($aColumns);

    $request = $app->request();

    $iDisplayStart = $database->escape_value($request->get('iDisplayStart'));
    $iDisplayLength = $database->escape_value($request->get('iDisplayLength'));

    $sLimit = "";
    if(isset($iDisplayStart) && $iDisplayLength != '-1' ) {
        $sLimit = "LIMIT ".  $iDisplayStart .", ". $iDisplayLength;
    }
    #echo $sLimit;


    $iSortCol_0 =  $database->escape_value($request->get('iSortCol_0'));
    $iSortingCols =  $database->escape_value($request->get('iSortingCols'));
    
    $sOrder = "";
    if (isset($iSortCol_0)) {
        $sOrder = "ORDER BY  ";

        #echo  $iSortCol_0."<br>";
        #echo   $iSortingCols."<br>";

        for ( $i=0 ; $i<intval($iSortingCols) ; $i++ ) {        

            $iSortingCol =  $database->escape_value($request->get('iSortCol_'.$i));
            $bSortable =  $database->escape_value($request->get('bSortable_'.intval($iSortingCol) ));

            #echo $iSortingCol."<br>";
            #echo  $bSortable."<br>";
            
            if ($bSortable == "true") {

                $iSortingCols =  $database->escape_value($request->get('sSortDir_'.$i));
                $sOrder .= "`".$aColumns[intval($iSortingCol)] ."` ". $iSortingCols .", ";

                #echo  $aColumns[intval($iSortingCol)]."<br>";
                #echo   $sOrder."<br>";
            }
            
        }
        
        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" ) {
            $sOrder = "";
        }
    }
    //echo $sOrder;


    $sSearch = $database->escape_value($request->get('sSearch'));

    $sWhere = "";
    if ( isset($sSearch) && $sSearch != "" )
    {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            $sWhere .= "`".$aColumns[$i]."` LIKE '%". $sSearch."%' OR ";
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }
    #echo $sWhere."<br>";
    
    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $bSearchable = $database->escape_value($request->get('bSearchable_'.$i));
        $sSearch = $database->escape_value($request->get('sSearch_'.$i));

        if ( isset($bSearchable) && $bSearchable == "true" && $sSearch != '' )
        {
            if ( $sWhere == "" ) {
                $sWhere = "WHERE ";
            } else {
                $sWhere .= " AND ";
            }
            $sWhere .= "`".$aColumns[$i]."` LIKE '%". $sSearch ."%' ";
        }
    }

    #echo $sWhere."<br>";

    $fsQuery = "SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."` FROM ". $table 
             ." $sWhere $sOrder $sLimit";

    //echo $sQuery;
    //$rResult =  $database->query($sQuery);
    $cTable = ucfirst($table);

    $oTable = $cTable::find_by_sql($fsQuery);

    $sQuery = "SELECT FOUND_ROWS()";
    $result = $database->query($sQuery);
    $row =  $database->fetch_row($result);
    $iFilteredTotal = intval($row[0]);

    $sQuery = "SELECT COUNT(`id`) FROM ". $table;
    $result = $database->query($sQuery);
    $row =  $database->fetch_row($result);
    $iTotal = intval($row[0]);

    #echo $iTotal;

    $sEcho = $database->escape_value($request->get('sEcho'));

    $output = array(
        "sql" => $fsQuery,
        "sEcho" => intval($sEcho),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => $oTable
    );
    
   
    #$output['aaData'][] = $categorys;


        
    
    
    echo json_encode( $output );

    




}




function datatables2($cTable) {
    
    $database = new MySQLDatabase();
    $app = \Slim\Slim::getInstance();

    $table = substr_replace($cTable, 'v', 0, 0);


    $sql = "DESCRIBE ". $table;
    //echo $sql ."<br>";
    $rows = $database->query($sql);

    //echo var_export($rows);

    $aColumns = array();

    while($row = $database->fetch_row($rows)) {
        $aColumns[] = $row[0];
    }

    //print_r($aColumns);

   // $key = array_search('id',  $aColumns); 
    #echo "<br>".$key."<br>";  

    //unset($aColumns[$key]);

    #print_r($aColumns);

    $request = $app->request();

    $iDisplayStart = $database->escape_value($request->get('iDisplayStart'));
    $iDisplayLength = $database->escape_value($request->get('iDisplayLength'));

    $sLimit = "";
    if(isset($iDisplayStart) && $iDisplayLength != '-1' ) {
        $sLimit = "LIMIT ".  $iDisplayStart .", ". $iDisplayLength;
    }
    #echo $sLimit;


    $iSortCol_0 =  $database->escape_value($request->get('iSortCol_0'));
    $iSortingCols =  $database->escape_value($request->get('iSortingCols'));
    
    $sOrder = "";
    if (isset($iSortCol_0)) {
        $sOrder = "ORDER BY  ";

        #echo  $iSortCol_0."<br>";
        #echo   $iSortingCols."<br>";

        for ( $i=0 ; $i<intval($iSortingCols) ; $i++ ) {        

            $iSortingCol =  $database->escape_value($request->get('iSortCol_'.$i));
            $bSortable =  $database->escape_value($request->get('bSortable_'.intval($iSortingCol) ));

            #echo $iSortingCol."<br>";
            #echo  $bSortable."<br>";
            
            if ($bSortable == "true") {

                $iSortingCols =  $database->escape_value($request->get('sSortDir_'.$i));
                $sOrder .= "`".$aColumns[intval($iSortingCol)] ."` ". $iSortingCols .", ";
                #$sOrder .= "`".$aColumns[intval($iSortingCol)+1] ."` ". $iSortingCols .", ";

                #echo  $aColumns[intval($iSortingCol)]."<br>";
                #echo   $sOrder."<br>";
            }
            
        }
        
        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" ) {
            $sOrder = "";
        }
    }
    #echo $sOrder;


    $sSearch = $database->escape_value($request->get('sSearch'));

    $sWhere = "";
    if ( isset($sSearch) && $sSearch != "" )
    {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            $sWhere .= "`".$aColumns[$i]."` LIKE '%". $sSearch."%' OR ";
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }
    #echo $sWhere."<br>";
    
    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $bSearchable = $database->escape_value($request->get('bSearchable_'.$i));
        $sSearch = $database->escape_value($request->get('sSearch_'.$i));

        if ( isset($bSearchable) && $bSearchable == "true" && $sSearch != '' )
        {
            if ( $sWhere == "" ) {
                $sWhere = "WHERE ";
            } else {
                $sWhere .= " AND ";
            }
            $sWhere .= "`".$aColumns[$i]."` LIKE '%". $sSearch ."%' ";
        }
    }

    #echo $sWhere."<br>";
    #$vTable = substr_replace($table, 'v', 0, 0);

    $fsQuery = "SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."` FROM ". $table 
             ." $sWhere $sOrder $sLimit";

    //echo $fsQuery;
    //$rResult =  $database->query($sQuery);
    $cTable = ucfirst($table);

    

    $oTable = $cTable::find_by_sql($fsQuery);

    //echo json_encode($oTable);

    $sQuery = "SELECT FOUND_ROWS()";
    $result = $database->query($sQuery);
    $row =  $database->fetch_row($result);
    $iFilteredTotal = intval($row[0]);

    $sQuery = "SELECT COUNT(`id`) FROM ". $table;
    $result = $database->query($sQuery);
    $row =  $database->fetch_row($result);
    $iTotal = intval($row[0]);


    #echo $iTotal;

    $sEcho = $database->escape_value($request->get('sEcho'));

    $output = array(
        "sql" => $fsQuery,
        "sEcho" => intval($sEcho),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => $oTable
    );
    
   
    #$output['aaData'][] = $categorys;


        
    
    
    echo json_encode( $output );

    
}



function searchTable($table) {

    $app = \Slim\Slim::getInstance();
    $database = MySQLDatabase::getInstance();

    $request = $app->request();

    $q = $database->escape_value($request->get('q'));
    $maxRows = $database->escape_value($request->get('maxRows'));
    $maxRows = isset($maxRows) ? $maxRows : 25;

    $sql = "SELECT * FROM ". $table ." WHERE ( `code` LIKE '%". $q ."%' OR 
                                                `descriptor` LIKE '%". $q ."%' 
                                        ) ORDER BY code asc LIMIT 0, ". $maxRows; 

    $sTable = ucfirst($table);
    
    $oTable = $sTable::find_by_sql($sql);


    echo json_encode( $oTable );

}



function getDetail($table) {

    $app = \Slim\Slim::getInstance();
    $sTable = ucfirst($table);

    $request = $app->request()->getBody();
    $get = json_decode($request, true);

    //$vTable = substr_replace($sTable, 'v', 0, 0);
    //$ovTable = new $vTable;

    
   /* foreach ($get as $key => $value) {

        if((substr_compare($key, "id", -2, 2)==0) && (substr($key,-2)=='id')) {
            $x = explode('id', $key); // itemid, becomes item
            $class_name = ucfirst($x[0]); // Item
            $object =  $class_name::find_by_id($value);
            $ovTable->$x[0] = $object->code;
        } else if($value==NULL || $value=="null" || is_null($value)) { 
            $ovTable->$key = "";
        } else {
            $ovTable->$key = $value;
        }
    } 
    
    echo json_encode($ovTable);   */


    $obj = (object) array();

    foreach ($get as $key => $value) {

        //if((substr_compare($key, "id", -2, 2)==0) && (substr($key,-2)=='id')) {
        if($key=='itemid') {
            
            $obj->$key = $value;
            $x = explode('id', $key); // itemid, becomes item
            $class_name = ucfirst($x[0]); // Item
            $object =  $class_name::find_by_id($value);
            $obj->$x[0] = $object->code;
           
        } else if($value==NULL || $value=="null" || is_null($value)) { 
            $obj->$key = "";
        } else {
            $obj->$key = $value;
        }
    } 


    echo json_encode($obj);   

    
    /*
    if($success) {

        $vTable = substr_replace($sTable, 'v', 0, 0);
        $ovTable = $vTable::find_by_id($oTable->id);
     
        echo json_encode($ovTable);    

    } else {
       echo '{"error":" error on saving '.mysql_error().'"}';
    }
    


    $output = array(
        "status" => "ok",
        "item" => "ok"

        );

    /*
    *  add record to a table but response with view table
    */
}






function authUserLogin() {

    $app = \Slim\Slim::getInstance();
    $session = Session::getInstance();
    //$database = MySQLDatabase::getInstance();
    global $database;
    
    $request = $app->request();
    $usr = $database->escape_value($request->post('username'));
    $pwd = $database->escape_value($request->post('password'));
    
    /*
    $request = $app->request()->getBody();
    $post = json_decode($request, true);
    $usr = $database->escape_value($post['username']);
    $pwd = $database->escape_value($post['password']);

    /*
    class User {
        public $username;
        public $password;
        public $personid;


        public function auth($u,$p){

            $this->username = 'admin';
            $this->$password = 'password';
            $this->$personid = '001';

            
                return $this;
           
        }
    }
    */
    //echo $usr;
    //echo $pwd;

    $found_user = User::auth($usr,$pwd);
    //echo var_dump($request->post('username'));

    if($found_user) {
    #if($usr == 'admin' && $pwd =='password') {

        $session->login($found_user);

        //$_SESSION['cid'] = '001';
        $session->set_fullname($found_user->descriptor);
    
       
        $app->redirect('/index');
        //$_SERVER['REMOTE_ADDR']=='127.0.0.1' ?  $app->redirect('/mfi-boss/index') : $app->redirect('/index'); 
    } else {
        $app->redirect('/login?error');
        //$_SERVER['REMOTE_ADDR']=='127.0.0.1' ?  $app->redirect('/mfi-boss/login?error') : $app->redirect('/login?error'); 
        //$app->redirect('/mfi-boss/login?error');
    }   
    
    //echo json_encode($respone);
}





function postDetail($table) {

    $app = \Slim\Slim::getInstance();
    $sTable = ucfirst($table);

    $request = $app->request()->getBody();
    $detail = json_decode($request, true);


    foreach($detail as $get) {

        $oTable = new $sTable();

        foreach ($get as $key => $value) {
            $oTable->$key = $value;
        }   

        $success = $oTable->create();
    }

    if($success){
        $respone = array(
            'status' => 'ok', 
            'code' => '200',
            'message' => 'success saving child data!'
        );
    } else {
        $respone = array(
            'status' => 'error', 
            'code' => '404',
            'message' => 'unable to save child data!'
        );
    }
    echo json_encode($respone);     
}





function putDetail($table,$table2,$apvhdrid) {

    $app = \Slim\Slim::getInstance();
    $database = MySQLDatabase::getInstance();
    $sTable = ucfirst($table); // apvdtl
    $sTable2 = ucfirst($table2); // apvhdr

    $request = $app->request()->getBody();
    $detail = json_decode($request, true);


    //check 1st if there is an existing table2 (parent table)
    //2nd if there is a record with that id
    $parent = $sTable2::find_by_id($apvhdrid);

    if(isset($parent) && $parent!=false) {
        $sTable::delete_all_by_field_id($table2,$apvhdrid); 
        
        foreach($detail as $get) {

            $oTable = new $sTable();

            foreach ($get as $key => $value) {
                $oTable->$key = $value;
            }   

        
            $success = $oTable->create();
        }


        $respone = array(
            'status' => 'ok', 
            'code' => '200',
            'message' => 'detail saved'
        );

    } else {
        $respone = array(
            'status' => 'error', 
            'code' => '404',
            'message' => 'the is no '. $table2 .'table found'
        );
    }
    echo json_encode($respone);    
}




/*****************************  Transactions **************************/


function postingApvhdr($id){

    $app = \Slim\Slim::getInstance();
    //$database = MySQLDatabase::getInstance();
    global $database;

    $database->startTransaction();

    $apvhdr = Apvhdr::find_by_id($id);

    //echo json_encode($apvhdr);
    if(!$apvhdr->cancelled || $apvhdr->cancelled==0) {

        if(!$apvhdr->posted || $apvhdr->posted==0) {

            $apvhdr_last = new Apvhdr();
            $apvhdr_last->posted = 1;
            $apvhdr_last->balance = $apvhdr->totamount;
            $apvhdr_last->id = $id;

            //$apvhdr_last->lock_record();
            
            if(!$apvhdr_last->save()){
                
                $database->rollback();
                echo json_encode($apvhdr_last->result_respone(1,'1136'));
                exit();
            }
        

            /*************** start: update apledger ***********/
            $last_apledger = Apledger::get_last_record($apvhdr->supplierid);
            $last_apledger_currbal = isset($last_apledger->currbal) ? $last_apledger->currbal:0;
           
            $apledger = new Apledger();
            $apledger->supplierid   = $apvhdr->supplierid;
            $apledger->txndate      = $apvhdr->date;
            $apledger->txncode      = 'APV';
            $apledger->txnrefno     = $apvhdr->refno;
            $apledger->amount       = $apvhdr->totamount;
            $apledger->prevbal      = $last_apledger_currbal;
            $apledger->currbal      = $apledger->get_currbal();
            
            if(!$apledger->save()){
        
            /*        
            $apledger = Apledger2::post('APV', $apvhdr->refno, $apvhdr->date, $apvhdr->totamount, $last_apledger_currbal, $apvhdr->supplierid);
            if(!$apledger) { 
            */   


                $database->rollback();
                echo json_encode($apledger->result_respone(1,'1156'));
                exit();
            }
            /*************** end: update apledger ***********/

            /*************** start: update apledger ***********/
            $last_apvledger = Apvledger::get_last_record($apvhdr->id);
            $last_apvledger_currbal = isset($last_apvledger->currbal) ? $last_apvledger->currbal:0;
           
            $apvledger = new Apvledger();
            $apvledger->apvhdrid     = $apvhdr->id;
            $apvledger->txndate      = $apvhdr->date;
            $apvledger->txncode      = 'APV';
            $apvledger->txnrefno     = $apvhdr->refno;
            $apvledger->amount       = $apvhdr->totamount;
            $apvledger->prevbal      = $last_apvledger_currbal;
            $apvledger->currbal      = $apvledger->get_currbal();
            
            if(!$apvledger->save()){


                $database->rollback();
                echo json_encode($apvledger->result_respone(1,'1156'));
                exit();
            }
            /*************** end: update apledger ***********/
        
        


            $supplier = Supplier::find_by_id($apvhdr->supplierid);   
            // $stu = supplier to be updated
            $stu = new Supplier();
            $stu->balance = $supplier->balance + $apledger->amount;
            $stu->id = $apvhdr->supplierid;
            
            if($stu->lock_record()) {

                if(!$stu->save()){
                    
                    /* commeted out
                    *       * unable to update the supplier
                    *       * no change on balace
                    *       * no item or the total amount of apv is 0
                    */

                    /*
                    $q = $database->last_query;
                    
                    $database->rollback(); 
                    echo json_encode($stu->result_respone(1,' Unable to post. No items/details found.'. $q ));
                    exit();
                    */

                }
            } else {
                $database->rollback();
                echo json_encode($stu->result_respone(2)); 
                exit();
            }

            /*
            $arItem = array();

            $apvdtl_items = Apvdtl::find_all_by_field_id('apvhdr',$id);
            if(!isset($apvdtl_items)) {
                foreach ($apvdtl_items as $apv_items) {
                    
                    $item = Item::find_by_id($apv_items->itemid);  

                    array_push($arItem, $item);
                    
                    if($item->is_product()){

                        // $itu = item to be updated
                        $itu = new Item();
                        $itu->onhand = $item->onhand + $apv_items->qty;
                        $itu->unitcost = $apv_items->unitcost;
                        $itu->id = $item->id;

                        if($itu->lock_record()) {

                            if(!$itu->save()){
                                
                                $database->rollback(); 
                                echo json_encode($itu->result_respone(1,'no item to save'. $item->id)); 
                                exit();
                            }
                        } else {

                            $database->rollback();
                            echo json_encode($itu->result_respone(2,'unable to lock item '. $itu->id));  
                            exit();
                        }


                        $last_stockcard = Stockcard::get_last_record($item->id);
                        $last_stockcard_currbal = isset($last_stockcard->currbal)  ? $last_stockcard->currbal:0;
                        //$last_stockcard->currbal = $item->onhand;

                        $stockcard = new Stockcard();
                        $stockcard->itemid      = $item->id;
                        $stockcard->locationid  = $apvhdr->locationid;
                        $stockcard->txndate     = $apvhdr->date;
                        $stockcard->txncode     = 'APV';
                        $stockcard->txnrefno    = $apvhdr->refno;
                        $stockcard->qty         = $apv_items->qty;
                        $stockcard->prevbal     = $last_stockcard_currbal;
                        $stockcard->currbal     = $stockcard->get_currbal();
                        //$stockcard->prevbalx    =;
                        //$stockcard->currbalx    =;

                        if($stockcard->lock_record()){

                            if(!$stockcard->save()){
                                
                                $database->rollback(); 
                                echo json_encode($stockcard->result_respone(1,'1240')); 
                                exit();
                            }
                        } else {
                             
                            $database->rollback();
                            echo json_encode($stockcard->result_respone(2)); 
                            exit();
                        }
                    } // end is product
                } // end foreach item
            } else {
                // no items/details on this apv
                // just continue the transaction
            }
            */

        
            //$database->rollback();
            if($database->commit()){
                
                $respone = array(
                    'status' => 'success', 
                    'code' => '200',
                    'message' => 'Success on posting: '. $apvhdr->refno ,
                    'data' => array(
                        'apvhdr' => $apvhdr_last 
                    )
                );
            } else {
                $database->rollback();

                $respone = array(
                    'status' => 'error', 
                    'code' => '404',
                    'message' => 'error on commtting the transactions',
                    'data' => array(
                        'apvhdr' => $apvhdr_last
                    )
                );
            }
        } else {

            $database->rollback();
            $respone = array(
                'status' => 'error', 
                'code' => '411',
                'message' => 'apvhdr '. $apvhdr->refno .' is already posted'
            );
        }
    } else {
        $database->rollback();
        $respone = array(
                'status' => 'error', 
                'code' => '412',
                'message' => 'apvhdr '. $apvhdr->refno .' is cancelled. Cannot be posted.'
        );
    }

    
    echo json_encode($respone); 
    


    //$database->rollback();

   // echo json_encode($apledger);


}



function postingCancelledApvhdr($id){

    $app = \Slim\Slim::getInstance();
    //$database = MySQLDatabase::getInstance();
    global $database;

    $apvhdr = Apvhdr::find_by_id($id);

    $apvhdr_last = new Apvhdr();
    $apvhdr_last->posted = 1;
    $apvhdr_last->cancelled = 1;
    $apvhdr_last->id = $id;

    if($apvhdr_last->save()){
        
        $respone = array(
                'status' => 'success', 
                'code' => '200',
                'message' => 'success on posting cancelled APV',
                'data' => array(
                    'apvhdr' => $apvhdr_last,
                )
            );
    } else {
        
        $respone = array(
            'status' => 'error', 
            'code' => '404',
            'message' => 'error on posting cancelled APV'
        );
    }

    echo json_encode($respone); 
}


function getPostedDetail($table, $fld) {

    $app = \Slim\Slim::getInstance();
    $sTable = ucfirst($table);

    $request = $app->request();
    //$get = json_decode($request, true);



    $oTable = $sTable::find_all_posted($field=$fld, $id=$request->get('id'), $posted=true);

    echo json_encode($oTable); 
}


function searchTxnViewTable($table, $supplierid) {

    $app = \Slim\Slim::getInstance();
    $database = MySQLDatabase::getInstance();

    $request = $app->request();

    $q = $database->escape_value($request->get('q'));
    $maxRows = $database->escape_value($request->get('maxRows'));
    $maxRows = isset($maxRows) ? $maxRows : 25;

    //$sTable = ucfirst($table);
    $vTable = substr_replace($table, 'v', 0, 0);
    //$ovTable = $vTable::find_by_id($oTable->id);

    $sql = "SELECT * FROM ". $table ." WHERE `refno` LIKE '%". $q ."%' AND posted = 1 AND cancelled = 0 AND balance > 0 AND supplierid = '". $supplierid ."' ORDER BY date DESC LIMIT 0, ". $maxRows; 

    //$sTable = ucfirst($table);
    
    $oTable = $vTable::find_by_sql($sql);


    echo json_encode( $oTable );
}


function findAllByFieldId($table, $field, $fieldid){

    $cTable = ucfirst($table);

    $oTable = $cTable::find_all_by_field_id($field, $fieldid);

    echo json_encode($oTable);

}


function queryReport(){

    $app = \Slim\Slim::getInstance();
    $database = MySQLDatabase::getInstance();

    $request = $app->request();

    $to = $database->escape_value($request->get('to'));
    $fr = $database->escape_value($request->get('fr'));

     $result = vApvhdr::queryReport($to, $fr);

    echo json_encode($result);


}



function apvGetDue(){
    $app = \Slim\Slim::getInstance();
    //$database = MySQLDatabase::getInstance();
    global $database;

    $request = $app->request();

    $due = $database->escape_value($request->get('due'));
    $posted = $database->escape_value($request->get('posted'));

    //$to = '2014-02-01';
    //$fr = '2014-02-15';
   

    $result = vApvhdr::getDue($due, $posted);
    //echo $database->last_query;

    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: X-Requested-With");
    header("Access-Control-Max-Age: 86400");

    echo json_encode($result);

}















