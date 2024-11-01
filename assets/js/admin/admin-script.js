jQuery(document).ready(function ($) {
    // ON CLICK INPUT BOC SHOTCODE WILL BE COPIED
    jQuery(".iusisu_a_click .iusisu_a_copy").click(function(){
        jQuery(this).closest("iusisu_a_click").find("span").text();
        document.execCommand("Copy");
        jQuery(this).next('.iusisu-copied-txt').show();
        setTimeout(function() { jQuery(".iusisu-copied-txt").hide(); }, 2500);
    });
    // END, ON CLICK INPUT BOC SHOTCODE WILL BE COPIED
    setTimeout(function () {
        var currentTab = localStorage.getItem('current_tab');
        var currentur = localStorage.getItem('current_usr');
        var chkur = admin_ajaxObj.curr_user;

        if (currentTab && currentur == chkur) {
            jQuery('.iusisu_a_tabs li[data-tab="' + currentTab + '"]').trigger('click');
        }
    }, 5);
    //nav tabs for theme options
   jQuery(document).on("click", 'ul.iusisu_a_tabs li', function (e) {
        localStorage.setItem('current_tab', jQuery(e.target).attr('data-tab'));
        //alert(localStorage);
        localStorage.setItem('current_usr', admin_ajaxObj.curr_user);
        var tab_id = jQuery(this).attr('data-tab');

        jQuery('ul.iusisu_a_tabs li.iusisu_a_tab-link').removeClass('iusisu_a_current');
        jQuery('.iusisu_a_tab-content').removeClass('iusisu_a_current');

        jQuery(this).addClass('iusisu_a_current');
        jQuery("#" + tab_id).addClass('iusisu_a_current');
    });
});