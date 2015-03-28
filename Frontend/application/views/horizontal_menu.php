<div style="position:absolute;right:0;top:-1px;height: 100%;background-color: #fff;width:3px;border-left:1px solid #ccc;border-top:1px solid #fff;border-bottom:1px solid #fff">
</div>
<div id="btnShowHideMenu" onclick="SetSideMenuVisibility()" style="cursor:pointer;position:absolute;right:8px;top:5px;height: 15px;width:15px;background:url('../asset/images/layout/arrow-31-512_close.png')">
</div>
<div id="tree-container">
<ul id="menu-tree" style="display: none">
  <li data-options="iconCls:'icon-ci'"><span>Configuration Items</span>
    <ul>
       <li data-options="<?php if ($active_CI['ci_type'] != CI_DIVISION) { ?>state:'closed',<?php } ?>iconCls:'icon-division'">
        <span><a href="<?php echo $base_url?>division/index">Division</a></span>
        <!-- <ul>
         <li data-options="iconCls:'icon-list'">
             <span><a href="<?php echo $base_url?>division/index/division_list">List</a></span>
             </li>
            <li data-options="iconCls:'icon-add'">Add New</li>
        </ul> -->
      </li>
      <li data-options="<?php if ($active_CI['ci_type'] != CI_DEPARTMENT) { ?>state:'closed',<?php } ?>iconCls:'icon-department'">
        <span><a href="<?php echo $base_url?>department/index">Department</a></span>
        <!-- <ul>
          <li data-options="iconCls:'icon-list'">
            <span><a href="<?php echo $base_url?>department/index/department_list">List</a></li>
          <li data-options="iconCls:'icon-add'">Add New</li>
        </ul> -->
      </li>
       <li data-options="<?php if ($active_CI['ci_type'] != CI_PRODUCT) { ?>state:'closed',<?php } ?>iconCls:'icon-product'">
       <span><a href="<?php echo $base_url?>product/index">Product</a></span>
        <!-- <ul>
          <li data-options="iconCls:'icon-list'">
            <span><a href="<?php echo $base_url?>product/index/product_list">List</a>
          </li>
          <li data-options="iconCls:'icon-add'">Add New</li>
        </ul> -->
      </li>
       <li data-options="<?php if ($active_CI['ci_type'] != CI_SERVER) { ?>state:'closed',<?php } ?>iconCls:'icon-server'">
       <span><a href="<?php echo $base_url?>server/index">Server</a></span>
        <!-- <ul>
         <li data-options="iconCls:'icon-list'">
           <span><a href="<?php echo $base_url?>server/index/server_list">List</a>
         </li>
          <li data-options="iconCls:'icon-add'">Add New</li>
        </ul> -->
      </li>

    </ul>
  </li>
</ul>
</div>