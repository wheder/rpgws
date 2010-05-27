<?php if($this->err) {?>
<h3>Chyba: <?php echo $this->msg; ?></h3>
<?php } else { ?>
<div id="description">
<p>
<?php echo htmlspecialchars($this->quest->description); ?>
<br />
<?php if($this->pj) { ?>
<a href="/drd/quest/manage/<?php echo $this->quest->quest_id; ?>">Spravovat quest</a>
<?php } ?>
</p>
</div>

<div id="form">
<form action="/drd/quest/add_post/<?php echo $this->quest->quest_id; ?>" method="post">
Text:<br />
<textarea rows="5" cols="40" name="content"></textarea>
<br />
Šepot: <input type="checkbox" name="whisp" value="1"/><br />
Cíl šeptání: 
<select name="targets[]" multiple="multiple">
<?php
if(!empty($this->chars)) foreach($this->chars as $char)
{
    echo "<option value=\"" . $char->character_id . "\">" . $char->name . "</option>\n";
}
?>
</select>
<br />
<input type="submit" value="Odeslat">
</form>
</div>
<div id="posts">
<?php foreach($this->posts as $post) {?>
	<div class="post">
		<div class="author">
			<?php echo ($post->author_character === null ? 'Pán jeskyně' : htmlspecialchars($post->author_character->name));?>
		</div>
		<div class="time">
			<?php echo $post->time; ?>
		</div>
		<?php if($post->is_whisper()) {?>
		<div class="whisper">
			Šepot
		</div>
		<?php } ?>
		<div class="post-content" ?>
		   <?php echo nl2br(htmlspecialchars($post->content)); ?>
		</div>
	</div>
<?php } ?>
</div>
<?php }?>