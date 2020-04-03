<{include file="db:yogurt_navbar.tpl"}>
<div id="yogurt-friends-container" class="outer">
    <h2 class="head"><{$lang_friendstitle}></h2>
    <{if $lang_nofriendsyet==""}>
    <{section name=i loop=$friends}>
        <div class="yogurt-friend <{cycle values="odd,even"}>">
            <p><a href="<{$xoops_url}>/modules/yogurt/index.php?uid=<{$friends[i].uid}>" alt=" <{$friends[i].uname}>" title="<{$friends[i].uname}>"> <{if $friends[i].user_avatar=="blank.gif" }>
                        <img src="assets/images/noavatar.gif">
                    <{else}>
                        <imgsrc
                        ="<{$xoops_upload_url}>/<{$friends[i].user_avatar}>"><{/if}><{$friends[i].uname}></a></p>
            <{if $isOwner }>
                <form action="editfriendship.php" method="post" class="yogurt-friends-deleteform">
                    <input type="hidden" name="friend_uid" id="friend_uid" value="<{$friends[i].uid}>">
                    <input name="submit" id="submit" src="assets/images/evaluate.gif" type="image" title="<{$lang_evaluate}>" alt="<{$lang_evaluate}>">
                </form>
                <form action="delfriendship.php" method="post" class="yogurt-friends-evaluateform">
                    <input type="hidden" name="friend_uid" id="friend_uid" value="<{$friends[i].uid}>">
                    <input name="submit" id="submit" src="assets/images/dele.gif" type="image" title="<{$lang_delete}>" alt="<{$lang_delete}>">
                </form>
            <{/if}>
        </div>
    <{/section}>
    <{else}>
    <h2 id="yogurt-friends-nofriends"><{$lang_nofriendsyet}></h2>
    <{ /if}>
</div>
<div style="clear:both"></div>
<{if $navegacao!='' }>
<div id="yogurt-navegacao"><{$navegacao}></div>
<{ /if}>
<{include file="db:yogurt_footer.tpl"}>