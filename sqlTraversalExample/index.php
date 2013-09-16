<?php
//define the header guard so we don't have direct access. Creates the white screen of death
define('HEADER_GUARD',true);
//define the absolute path
define('APP_ROOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
require_once APP_ROOT_PATH . 'bootstrap.php';

$notes = array();
if($query = $mysqli->query("SELECT * FROM notes ORDER BY id DESC"))
{
	while($note_row=$query->fetch_object()){
	$notes[$note_row->id] = $note_row;
	}
	$query->close();
}


?>
<!DOCTYPE html>

<html>
<head>
<title>Notes list</title>
</head>

<body>
	<div class="wrapper">
	  <header id="header">
		<h1 class="site-name">My notes</h1>
		<section id="main">
			<p>You can view a note that is listed below by clicking on the title. </p>
			<?php if(!empty($notes)) :?>
			<ul class="notes-list">
			<?php foreach($notes as $note) : ?>
			<li class="notes-list-note-id-<?php print $note->id; ?>">
				<h3><a href="viewnote.php?nid=<?php print $note->id;?>"title="<?php print $note->title; ?>"><?php print
					$note->title;?></a></h3>
			</li>
			<?php endforeach;?>
			</ul>
			<?php else : ?>
			<p> There is currently no notes on the database. </p>
			<?php endif; ?>
			</section>
			<footer id = "footer">
			&nbsp;
			</footer>
		</div>
</body>
</html>

