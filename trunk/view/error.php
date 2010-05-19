<h1><?php echo $this->head; ?></h1>
<p><?php echo $this->message; ?></p>
<hr />
<p><pre><?php
    if (RPGWS_ENVINRONMENT === 'debug') echo $this->debug_info;
?></pre></p>
<p>
    Kód chyby: <?php echo $this->code; ?>
</p>
