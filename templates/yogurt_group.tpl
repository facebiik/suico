<{include file="db:yogurt_navbar.tpl"}>

<div id="yogurt-group-container" class='outer'>

    <h5><{$group_title}></h5>

    <p id="yogurt-group-img" class=odd>
        <img src="<{$xoops_upload_url}>/yogurt/groups/<{$group_img}>">
    </p>

    <p id="yogurt-group-desc" class="even">
        <{$group_desc}><br>
		<b><{$lang_ownerofgroup}></b><br>
		<{$group_ownername}><br>
		
		<{if $isOwner }>
        <{if $xoops_userid == $group_owneruid }>
                            
					 <form action="delete_group.php" method="POST" id="form_deletegroup" class="yogurt-groups-form-delete">
                        <input type="hidden" value="<{$group_id}>" name="group_id" id="group_id">
                        <input type="image" src="<{xoModuleIcons16 delete.png}>">
                    </form>
                    <form action="editgroup.php" method="POST" id="form_editgroup" class="yogurt-groups-form-edit">
                        <input type="hidden" value="<{$group_id}>" name="group_id" id="group_id">
                        <input type="image" src="<{xoModuleIcons16 edit.png}>">
                    </form>
                          <{/if}>
              
                <{/if}>
		
		
		<{if $isAnonym!=1}>
		<{if $memberOfGroup ==1}>
    <form action="abandongroup.php" method="POST" id="form_abandongroup">
        <input type="hidden" value="<{$group_rel_id}>" name="relgroup_id" id="relgroup_id">
		<input type="hidden" value="<{$group_id}>" name="group_id" id="group_id">
        <button name="" type="image" class="btn btn-danger btn-sm"><{$lang_abandongroup}></button>
    </form>
    <{ else}>
    <form action="becomemembergroup.php" method="POST" id="form_becomemember" class="yogurt-groups-form-becomemember">
        <input type="hidden" value="<{$group_id}>" name="group_id" id="group_id">
        <button name="" type="image" class="btn btn-dark btn-sm"><{$lang_joingroup}></button>
	</form>
    <{/if}>
<{/if}>
    </p>
<br>
        <div id="yogurt-group-edit-members" class="outer odd">
            <h4 class="head"><{$lang_membersofgroup}></h4>
            <{section name=i loop=$group_members}>
                <div class="yogurt-group-edit-member <{cycle values="odd,even"}>">
                    <h4 class="head">
                        <a href="<{$xoops_url}>/modules/yogurt/index.php?uid=<{$group_members[i].uid}>" alt="<{$group_members[i].uname}>" title="<{$group_members[i].uname}>"><{$group_members[i].uname}></a>
                        <{if $group_owneruid==$group_members[i].uid}><img src="assets/images/owner.gif" alt="<{$lang_owner}>" title="<{$lang_owner}>"><{/if}>
                    </h4>
                    <p>
                        <a href="<{$xoops_url}>/modules/yogurt/index.php?uid=<{$group_members[i].uid}>" alt="<{$group_members[i].uname}>" title="<{$group_members[i].uname}>">
                            <{if $group_members[i].avatar=="avatars/blank.gif"}><img src="assets/images/noavatar.gif"><{else}> <img src="<{$xoops_upload_url}>/<{$group_members[i].avatar}>"><{/if}></a>

                        <{if $group_owneruid==$useruid && $group_owneruid!=$group_members[i].uid}>
                    <form action="kickfromgroup.php" method="post">
                        <input type="hidden" value="<{$group_id}>" name="group_id" id="group_id">
                        <input type="hidden" value="<{$group_members[i].uid}>" name="rel_user_uid" id="rel_user_uid">
                         <button name="" type="image"><{$lang_removemember}></button>
                    </form>
                    <{/if}>
                    </p>
                </div>
            <{/section}>
        </div>
    
</div>

    <div style="clear:both;">
        <{$commentsnav}>
        <{$lang_notice}>
    </div>
    <div style="clear:both;">
        <{if $comment_mode == "flat"}>
            <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
            <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
            <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
    </div>
    <{include file="db:yogurt_footer.tpl"}>