    <p>Udfyld felterne for at sende et elektronisk julekort. </p>
    <form role="form" method="post" action="<?php print $_SERVER['REQUEST_URI'];?>">
    <div class="row">
  <div class="form-group col-md-6">
    <label for="sender_name">Dit navn</label>
    <input type="text" class="form-control input-sm" id="sender_name" name="sender_name" placeholder="Dit navn" <?php print errorclass($errors, 'sender_name')?> <?php print currentvalue('sender_name')?> >
  </div>
  

    <div class="form-group col-md-6">
      <label for="sender_email">Din e-mail</label>
      <input type="email" class="form-control input-sm" id="sender_email" name="sender_email" placeholder="Din e-mail" <?php print errorclass($errors, 'sender_email')?> <?php print currentvalue('sender_email')?> >
    </div>

  </div>  

  <div class="row ">

    <div class="form-group col-md-6">
      <label for="recipient_name">Modtagers navn</label>
      <input type="text" class="form-control input-sm" id="recipient_name" name="recipient_name" placeholder="Modtagers navn" <?php print errorclass($errors, 'recipient_name')?> <?php print currentvalue('recipient_name')?>>
    </div>

  
  
    <div class="form-group col-md-6">
      <label for="recipient_email">Modtagers e-mail</label>
      <input type="email" class="form-control input-sm" id="recipient_email" name="recipient_email" placeholder="Modtagers e-mail" <?php print errorclass($errors, 'recipient_email')?> <?php print currentvalue('recipient_email')?>>
    </div>

  </div>

  <div class="form-group">
    <label for="message">Din julehilsen</label>
    <textarea class="form-control" rows="10" name="message" placeholder="Indtast din julehilsen" <?php print errorclass($errors, 'message')?>><?php print currentvalue('message', FALSE)?></textarea>
  </div>


  <div class="checkbox">
    <label>
      <input type="checkbox">TODO: Send mig en mail når kortet er blevet læst
    </label>
  </div>

  FIXME
  <input type=submit>
  FIXME

  <div class="form-group border-top">
    
    <a href="preview.php"  class="btn btn-default btn-xs">Preview kort </a>
    <a href="confirmation.php" class="btn btn-success btn-xs pull-right">Send kort</a>      
    
  </div>

  
  
</form>
