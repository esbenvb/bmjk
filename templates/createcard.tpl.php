    <p>Udfyld felterne for at sende et elektronisk julekort. </p>
    <form role="form" method="post" action="<?php print $_SERVER['REQUEST_URI'];?>">
    <div class="row">
  <div class="form-group col-md-6">
    <label for="sender_name">Dit navn</label>
    <input type="text" class="form-control input-sm <?php print errorclass($errors, 'sender_name')?>" id="sender_name" name="sender_name" placeholder="Dit navn"  <?php print currentvalue('sender_name')?> >
  </div>
  

    <div class="form-group col-md-6">
      <label for="sender_email">Din e-mail</label>
      <input type="email" class="form-control input-sm <?php print errorclass($errors, 'sender_email')?>" id="sender_email" name="sender_email" placeholder="Din e-mail"  <?php print currentvalue('sender_email')?> >
    </div>

  </div>  

  <div class="row ">

    <div class="form-group col-md-6">
      <label for="recipient_name">Modtagers navn</label>
      <input type="text" class="form-control input-sm <?php print errorclass($errors, 'recipient_name')?>" id="recipient_name" name="recipient_name" placeholder="Modtagers navn"  <?php print currentvalue('recipient_name')?>>
    </div>

  
  
    <div class="form-group col-md-6">
      <label for="recipient_email">Modtagers e-mail</label>
      <input type="email" class="form-control input-sm <?php print errorclass($errors, 'recipient_email')?>" id="recipient_email" name="recipient_email" placeholder="Modtagers e-mail"  <?php print currentvalue('recipient_email')?>>
    </div>

  </div>

  <div class="form-group">
    <label for="message">Din julehilsen</label>
    <textarea class="form-control <?php print errorclass($errors, 'message')?>" rows="10" name="message" placeholder="Indtast din julehilsen" ><?php print currentvalue('message', FALSE)?></textarea>
  </div>

<!--
  <div class="checkbox">
    <label>
      <input type="checkbox">TODO: Send mig en mail når kortet er blevet læst
    </label>
  </div>
-->
  <div class="form-group border-top">
    <input type="submit" name="op" class="btn btn-default btn-xs" value="<?php print $preview_button; ?>" />
    <input type="submit" name="op" class="btn btn-success btn-xs pull-right" value="<?php print $send_button; ?>" />
    
  </div>

  
  
</form>
