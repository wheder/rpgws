<h2>Seznam vašich postav</h2>
<ul>
<?php if(!empty($this->characters)) foreach($this->characters as $char) { ?>
<li><a href="/drd/character/modify_form/<?php echo $char->name; ?>"><?php echo $char->name; ?></a></li>
<?php }?>
</ul>
<p>
<a href="/drd/character/create_form/">Nová postava</a>
</p>