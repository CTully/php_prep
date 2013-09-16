<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL | E_STRICT);
define('HEADER_GUARD',true);

define('APP_ROOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
require_once APP_ROOT_PATH . 'bootstrap.php';
define('FILES_PATH', APP_ROOT_PATH . 'files' . DIRECTORY_SEPARATOR);

//defining some key values.
$note_id = isset($_GET['nid']) ? intval($_GET['nid']) : 0;
$mode = isset($_GET['mode']) ? $_GET['mode'] : null;
$modeFile =isset($_GET['file']) ? $_GET['file'] : null;



$note = null;
$has_added = false;
$has_edited=false;
$title =null;
$message = null;
$has_error = null;
$has_file= null;

$date = new DateTime(null);
$time_id  = $date->getTimestamp();

//Creating the Meta data for the file
$serializedMeta = array();
$ser_meta1 = null;
$ser_meta2 = null;

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Start upload work here
if( !defined('UPLOAD_ERR_EMPTY') )
{
	define('UPLOAD_ERR_EMPTY', 5);
}

$lang = array(
	'upload_error' => array(
		1 => 'UPLOAD_ERR_INI_SIZE',
		2 => 'UPLOAD_ERR_FORM_SIZE',
		3 => 'UPLOAD_ERR_PARTIAL',
		4 => 'UPLOAD_ERR_NO_FILE',
		UPLOAD_ERR_EMPTY => 'UPLOAD_ERR_EMPTY',
		6 => 'UPLOAD_ERR_NO_TMP_DIR',
		7 => 'UPLOAD_ERR_CANT_WRITE',
		8 => 'UPLOAD_ERR_EXTENSION'
	)
);

if( isset($_POST['submit']) ) {	
//upload work

$has_error = false;
$file = isset($_FILES['file']) ? $_FILES['file'] : array();
//~~~~~~~~~~~~~~~~~
if (!empty($file)){

if( UPLOAD_ERR_OK != $file['error'] ) {
		// $message = 'The file upload was not successful. Error: ' . $file['error'];
		$message = $lang['upload_error'][$file['error']];
		$has_error = true;
	}
else{

	if( 0 == $file['size'] ) {
			$message = $lang['upload_error'][UPLOAD_ERR_EMPTY];
			$has_error = true;
		}

}

if( !$has_error )
	{
		if( file_exists($file['tmp_name']) )
		{
			//chmod($file['tmp_name'], 755);
			//chmod(FILES_PATH, 777);

			$file_pieces = explode('.', $file['name']);
			$extension = array_pop($file_pieces);
			
		
			
			unset($file_pieces); // cleanup the namespace

			$name = uniqid(time() . '_', true);
			//Moving the file
			

			$destination = FILES_PATH . $name . '.' . $extension;
			
			if( !move_uploaded_file($file['tmp_name'], $destination) ) {
				$message = 'Unable to move file to destination.';
				$has_error = true;
				
			}
			else{
			  
			$has_file=true;
			$meta = serialize(array($file['size'],$file['type']));
			
			
			}
		}
	}
if( !$has_error )
	{
		$message = 'The file has been uploaded successfully.';
	}
}
	
	//"INSERT INTO file (path) VALUES ('" . mysql_real_escape_string($path) . "')";

//~~~~~~~~~~~~~~~~~~~~~~~~~~~END UPLOAD WORK~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	$title = isset($_POST['title']) ? trim($_POST['title']) : null;
	$message = isset($_POST['message']) ? trim($_POST['message']) : null;
	$added = $mysqli->prepare("INSERT INTO notes (created,title,message) VALUES(?,?,?)");		
	$addedFile = $mysqli->prepare("INSERT INTO notes_attachment (note_id,file_name,file_meta) VALUES(?,?,?)");
	
	if( $added ) {    
	$added->bind_param('dss', $time_id, $title,$message);
	$added->execute();
	//meta serialize the size element, type eletment
	//need to edit this part to determine if a file is uprated
	if($has_file){
	
	    $addedFile->bind_param('dss', $name, $name,$meta);
		$addedFile->execute();
				 }
	}

$lastid =$mysqli->insert_id;
$newNote ="<a href=\"viewnote.php?nid=$lastid\">New Message</a>";
$has_added = true;
}
if( isset($_POST['submit']) ) 
{
$edtmessage = isset($_POST['message']) ? trim($_POST['message']) : null;
$editedmsg = $mysqli->prepare("UPDATE notes set message=? WHERE id=?");	

	if( $editedmsg ) {    
		$editedmsg->bind_param('sd',  $edtmessage,$note_id );
		$editedmsg->execute();
		
	                 }
$editedNote ="<a href=\"viewnote.php?nid=$note_id\">Edited Message</a>";
$has_edited = true;
					}
?>


<html>
  <head>
	<title> Edit Note </title>
<body>
<h3> Edit, or add a note </h3>
<div id="messages">
<?php if( !empty($message) ) : ?>
<p><?php print $message; ?></p>
<?php endif;?>
</div>
	<?php if($mode == 'add') : ?>
	 <h1>Adding a note </h1>
<!--Add area -->
  <div class="added">
    <form method="post" enctype="multipart/form-data">
		<ul>
		<li><input type="text" name="title" placeholder="Title" maxlength="25" /> </li>
		<li><input type="text" name="message" placeholder="Message" maxlength="255"/></li>
		<li><input type="submit" name="submit" value="Commit Message" /></li>
	<hr />
	<!-- this part will allow you to upload what?
		<li><input type="submit" name="submit" value="Upload" /></li>
		<li><input type="text" name="title" method="post" /></li>-->		
		<p>
		<label for="file">File</label>
		<input type="file" name="file" id="file" />
	    </p>
		</ul>
      
	</form>
</div>
		 <?php if($has_added) { print $newNote;} ?>

<?php elseif($mode =='edit') :?>
	<!-- edit area -->
	<div class="edited">
		<h1>Editing a note </h1>
		<form method="post" enctype="multipart/form-data">
		<ul>
			<li><input type="text" name="message" placeholder="Message" maxlength="255" /></li>
				<li><input type="submit" name="submit" value="Commit Update"  /></li>
			 

		
		<?php if($modeFile == true) :?>
		<li>
		<p>
		<label for="file">File</label>
		<input type="file" name="file" id="file" />
	    </p>
		</li>	  
		
		<?php endif; ?>

		  </ul>
		  </form>
	   <?php if($has_edited) { print $editedNote;} ?>
		</div>
	   <?php else : ?>
			<p>There is currently no notes on the database. </p>
			
<?php endif; ?>
	
	



</body>
</head>
</html>


