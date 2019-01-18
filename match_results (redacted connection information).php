<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
<link rel="stylesheet" type="text/css" href="mystyle.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">
<body>

<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="home.html">Home</a>
  <a href="apps.html">College Applications</a>
  <a href="faid.html">Financial Aid</a>
  <a href="scholarships.html">Scholarships</a>
  <a href="match.html">College Match</a>
  <a href="ncaa.html">NCAA Eligibility</a>
  <a href="act-sat.html">ACT/SAT Registration</a>
</div> 

<div id="main">
  <span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776; Menu</span>


<?php  //open pdo connection to SQL database
$dsn = 'mysql: host=178.128.68.104;dbname=cypress;port=3306';
$username = 'XXXX';
$password = 'XXXX';

$pdo = new PDO($dsn, $username, $password);

try{$id = $_POST["id"];}catch(Exception $e){}  //see if page is being retreived from php form submission   

if(isset($_GET["data"])){
    $id = $_GET["data"];
}

$id = preg_replace("/[^0-9]/", "", $id); //scrub non-numeric entries
	
$sql = "SELECT * FROM cy5 WHERE id='$id'";
$stmt = $pdo->query($sql);
$row =$stmt->fetchObject();
        
$sql2 = "SELECT COUNT(*) AS 'rowcount' FROM cy5 WHERE id='$id'";
$stmt2 = $pdo->query($sql2);
$num = $stmt2->fetchObject();
?>

<br>

<?php
if( $num->rowcount ==0) //check if query returned a row
{
?>
<p>
    It appears you have entered an ID number I don't have on file. Please try again or come speak with me in the College & Career Center!
</p>
<?php
}else{
?>


<p> 
    Hello <?php echo $row->fname; ?> <?php echo $row->lname; ?>!
</p>


<p>
    According to my records from the end of last year, you are currently ranked <?php echo $row->rank; ?> out of the 731 students in your class. 
    This places you in the top <?php echo $row->percentile; ?> percent of the senior class.
</p>


<?php
if( $row->composite == 0) //check if student has ACT score in database
{
?>
<p>
    Unfortunately, I do not have ACT scores on file for you, so I can't make any college reccomendations at this time. 
    Please come speak with me in the College and Career Center if you would like some more information! 
</p>
<?php
}
?>


<?php
if( $row->composite != 0)
{
?>

<p>
    Last year when you took the ACT you had an overall composite score of <?php echo $row->composite; ?>, and you made the following scores in each section:
</p>

<p>
    English: <?php echo $row->english; ?> <br>
    Math: <?php echo $row->math; ?> <br>
    Reading: <?php echo $row->reading; ?> <br>
    Science: <?php echo $row->science; ?> <br>
    Writing: <?php echo $row->writing; ?> <br>
</p>


<?php

if( $row->takesat == 'Suggest SAT'){?>
<p>
    It appears that your science score is lower than your composite ACT score, which means you may want to consider trying the SAT which does not have a science section.
</p>
<?php
}	

if( $row->lunch_status == 'Yes'){?>
<p>
    Since you qualify for Free/Reduced lunch, you are eligible to recieve a fee waiver for the SAT, which may be picked up from me in the College and Career Center (Panther Store). 
</p>
<?php
}	

if( $row->mismatch == 'Score lower than expected'){?>
<p> 
    It appears that your test scores are somewhat lower than would be expected given your class ranking. I highly suggest that you either retake the ACT or take the SAT. 
</p>
<?php
}
?>	
<br>


<?php //check if student is eligible for automatic admission at any schools
if( $row->sum_admit != 0){?> 
<p>
    Based on this information, you should be elligible for automatic admission to the following schools. Clicking on each school will take you to their website.
</p>
<br>

<?php
class College {
    private $school_name, $school_url, $sql_row, $act_q1, $act_q3;

    public function __construct($school_name, $school_url, $sql_column, $act_q1, $act_q3){
        $this->school_name = $school_name;
        $this->school_url = $school_url;
        $this->sql_column = $sql_column;
        $this->act_q1 = $act_q1;
        $this->act_q3 = $act_q3;
    }

    public function autoAdmit(){
        if( $this->sql_column == 'TRUE'){
            ?><a href=<?php echo $this->school_url; ?> target-"_blank"><?php echo $this->school_name;?></a><br><?php
        }
    }

    public function bestFit($composite){
        if($composite >= $this->act_q1 and $composite <= $this->act_q3){
            ?><a href=<?php echo $this->school_url; ?> target="_blank"><?php echo $this->school_name ?></a> : Best Fit <br><?php
        }
        if($composite > $this->act_q3){
            ?><a href=<?php echo $this->school_url; ?> target="_blank"><?php echo $this->school_name ?></a> : Undermatch <br><?php
        }
    }
}

$ut = new College("University of Texas", "https://www.utexas.edu/", $row->ut, 25, 31);
$tamu = new College("Texas A&ampM University", "https://www.tamu.edu/", $row->tamu, 25, 30);
$utd = new College("University of Texas - Dallas", "https://www.utdallas.edu/", $row->utd, 25, 31);
$uhmain = new College("University of Houston", "https://www.uh.edu/", $row->uhmain, 23, 28);
$tech = new College("Texas Tech University", "https://www.ttu.edu/", $row->tech, 23, 27);
$thomas = new College("University of St. Thomas", "https://www.stthom.edu/", $row->thomas, 22, 27);
$tstate = new College("Texas State University - San Marcos", "https://www.txstate.edu/", $row->tstate, 21, 25);
$unt = new College("University of North Texas", "https://www.unt.edu/", $row->unt, 20, 26);
$uta = new College("University of Texas - Arlington", "https://www.uta.edu/uta/", $row->uta, 20, 26);
$utsa = new College("University of Texas - San Antonio", "https://www.utsa.edu/", $row->utsa, 20, 25);
$utt = new College("University of Texas - Tyler", "https://www.uttyler.edu/", $row->utt, 20, 25);
$shsu = new College("Sam Houston State University", "https://www.shsu.edu/", $row->shsu, 19, 24);
$sfa = new College("Stephen F. Austin University", "http://www.sfasu.edu/", $row->sfa, 19, 24);
$angelo = new College("Angelo State University", "https://www.angelo.edu/", $row->angelo, 18, 23);
$lamar = new College("Lamar University", "https://www.lamar.edu/", $row->lamar, 18, 23);
$wtamu = new College("West Texas A&ampM University", "https://www.wtamu.edu/", $row->wtamu, 18, 23);
$tarleton = new College("Tarleton State University", "https://www.tarleton.edu/", $row->tarleton, 18, 24);
$tamuc = new College("Texas A&ampM University - Commerce", "https://www.tamuc.edu/", $row->tamuc, 18, 24);
$tamucc = new College("Texas A&ampM University - Corpus Christi", "https://www.tamucc.edu/", $row->tamucc, 18, 23);
$utpb = new College("University of Texas - Permian Besin", "https://www.utpb.edu/", $row->utpb, 18, 22);
$tamuk = new College("Texas A&ampM University Kingsville", "https://www.tamuk.edu/", $row->tamuk, 17, 22);
$twu = new College("Texas Woman's University", "https://twu.edu/", $row->twu, 17, 23);
$utep = new College("University of Texas - El Paso", "https://www.utep.edu/", $row->utep, 17, 22);
$tamui = new College("Texas A&ampM University International", "https://www.tamiu.edu/", $row->tamui, 16, 20);
$sul_ross = new College("Sul Ross State University", "https://www.sulross.edu/", $row->sul_ross, 16, 20);
$uhd = new College("University of Houston - Downtown", "https://www.uhd.edu/", $row->uhd, 16, 20);
$pvamu = new College("Prarie View A&ampM University", "https://www.pvamu.edu/", $row->pvamu, 16, 20);
$tsu = new College("Texas Southern University", "http://www.tsu.edu/", $row->tsu, 15, 18);
$uhv = new College("University of Houston - Victoria", "https://www.uhv.edu/", $row->uhv, 15, 19);


$ut->autoAdmit();
$tamu->autoAdmit();
$utd->autoAdmit();
$uhmain->autoAdmit();
$tech->autoAdmit();
$thomas->autoAdmit();
$tstate->autoAdmit();
$unt->autoAdmit();
$uta->autoAdmit();
$utsa->autoAdmit();
$utt->autoAdmit();
$shsu->autoAdmit();
$sfa->autoAdmit();
$angelo->autoAdmit();
$lamar->autoAdmit();
$wtamu->autoAdmit();
$tarleton->autoAdmit();
$tamuc->autoAdmit();
$tamucc->autoAdmit();
$utpb->autoAdmit();
$tamuk->autoAdmit();
$twu->autoAdmit();
$utep->autoAdmit();
$tamui->autoAdmit();
$sul_ross->autoAdmit();
$uhd->autoAdmit();
$pvamu->autoAdmit();
$tsu->autoAdmit(); 
$uhv->autoAdmit();


}
}


if( $row->sum_admit == 0 and $row->composite != 0 ){ ?>
It appears that at this time you are not eligible to automatic admission for the schools I have included in this webpage. However, don't let this discourage you from applying to college! Many students that attend college are not automatically admitted, and you definitely still have a chance, especially if you retake the ACT or SAT. Please come speak with me in the College and Career Center for more information.
<?php
}

?>

<br><br>

<?php //check if student matches as "best fit" to any schools
if($row->composite >= 15)
{?>

<p>
    Another useful thing to keep in mind when considering picking a college is the idea of academic fit. You don't want to go to a school that is far too difficult for you, 
    but you also don't want to go to a school that is much too easy. This is the essential idea behind finding colleges that are a "best fit" match, where a student falls in 
    the middle 50% of test scores, where a student is likely to be accepted and perform well. Below are some schools and whether they are a "best fit", or an "undermatch" (meaning 
    the school may be too easy). 
</p>

 
<p>
    Realize that this isn't a hard yes or no, and that if you want to apply somewhere you don't see listed, you definitely should! It may be challenging, but many students are
    accepted to and succeed at schools where their test scores may be below average. No standardized test will fully determine where you go to school, and admissions departments
    look carefully at your involvement and essays. This is simply meant to give you an idea of where to start. 
</p> <br>

<p> 
    Here are your best fit results: 
</p>
<?php

$ut->bestFit($row->composite);
$tamu->bestFit($row->composite);
$utd->bestFit($row->composite);
$uhmain->bestFit($row->composite);
$tech->bestFit($row->composite);
$thomas->bestFit($row->composite);
$tstate->bestFit($row->composite);
$unt->bestFit($row->composite);
$uta->bestFit($row->composite);
$utsa->bestFit($row->composite);
$utt->bestFit($row->composite);
$shsu->bestFit($row->composite);
$sfa->bestFit($row->composite);
$angelo->bestFit($row->composite);
$lamar->bestFit($row->composite);
$wtamu->bestFit($row->composite);
$tarleton->bestFit($row->composite);
$tamuc->bestFit($row->composite);
$tamucc->bestFit($row->composite);
$utpb->bestFit($row->composite);
$tamuk->bestFit($row->composite);
$twu->bestFit($row->composite);
$utep->bestFit($row->composite);
$tamui->bestFit($row->composite);
$sul_ross->bestFit($row->composite);
$uhd->bestFit($row->composite);
$pvamu->bestFit($row->composite);
$tsu->bestFit($row->composite);
$uhv->bestFit($row->composite);

}


//send email with timestamp and link to student information
require '/usr/share/php/libphp-phpmailer/PHPMailerAutoload.php';
$mail = new PHPMailer;

if($_SERVER['HTTP_REFERER']=='http://178.128.68.14/match.html'){ //check to see if page was request from match tool to determine if email should be sent

try {
    //Server settings
    //$mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'chris.glenn.henson@gmail.com';
    $mail->Password = 'XXXX';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;


    //$mail->setFrom('', 'Admin');
    $mail->addAddress('chenson@tamu.edu', 'Recipient1');

    //Content
    $mail->isHTML(true);
    $mail->Subject = "$row->fname $row->lname";
    date_default_timezone_set('America/Chicago');
    $datetime = date('m/d/Y h:i:s a', time());
    $mail->Body    = 

"<p> $row->fname $row->lname (ID: $row->id) used the College Match tool at $datetime </p> 
 <a href='http://178.128.68.14/match_results.php?data=$id' target='_blank'>Information Shown to Student</a>  ";


    $mail->send();
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
}
}
?>

<script>
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("main").style.marginLeft= "0";
}
</script>
