<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
    <?php  foreach ($arrPermissionName as $oPn) { ?>
    <option value="<?php echo $oPn['id'];  ?>"><?php echo $oPn['name'] ?></option>
    <?php } ?>
</select>