<h2>Seznam questů</h2>
<ul>
<?php if(!empty($this->quests)) foreach($this->quests AS $quest) {?>
	<li><a href="/drd/quest/view/<?php echo $quest->quest_id; ?>"><?php echo $quest->description; ?></a></li>
<?php }?>
</ul>
<p>
<a href="/dq/quest/create_form/">Nový quest</a>
</p>