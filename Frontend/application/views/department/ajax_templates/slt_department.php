<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
<?php if (!empty($arrDepartment)) { ?>
    <?php  foreach ($arrDepartment as $oDepartment) { ?>
    <?php if ($oDepartment['alias'] != null && $oDepartment['alias'] != "") { ?>
    <option value="<?php echo $oDepartment['_id']  ?>"><?php echo $oDepartment['alias'] ?></option>
    <?php } ?> 
    <?php } ?> 
    <?php } else { ?>
    <option value="">--Select Department--</option>
    <?php } ?> 
</select>