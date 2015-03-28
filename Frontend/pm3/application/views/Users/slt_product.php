<?php if($this->input->get('type')=='json'){ ?>
<?php
$arrOption = array();
foreach($arrProduct as $oProduct) {
	$arrOption[] = array('value' => (string)$oProduct['_id'], 'text' => $oProduct['alias'], 'group' => (string)$oProduct['department_id']);
}
echo json_encode($arrOption);
?>
<?php } else { ?>
<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
	<option value=""></option>
	<?php foreach($arrProduct as $oProduct) { ?>
	<option value="<?php echo (string)$oProduct['_id'] ?>"><?php echo $oProduct['alias'] ?></option>
	<?php } ?>
</select>
<?php } ?>