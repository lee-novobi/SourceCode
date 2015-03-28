<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
    <?php  foreach ($arrDepartment as $oDepartment) { ?>
    <option <?php if ($nSelectedDepartment == $oDepartment['id']) { ?>selected="selected"<?php } ?> value="<?php echo $oDepartment['id'];  ?>"><?php echo $oDepartment['name'] ?></option>
    <?php } ?>
</select>