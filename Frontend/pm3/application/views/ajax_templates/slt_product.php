<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
    <?php foreach ($arrProduct as $oProduct) { ?>
    <option <?php if ($nSelectedProduct == $oProduct['id']) { ?>selected="selected"<?php } ?> value="<?php echo $oProduct['id'] ?>"><?php echo $oProduct['name'] ?></option>
    <?php } ?>
</select>