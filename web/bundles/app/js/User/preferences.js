/* freepost
 * http://freepo.st
 *
 * Copyright © 2014-2015 zPlus
 * 
 * This file is part of freepost.
 * freepost is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * freepost is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with freepost. If not, see <http://www.gnu.org/licenses/>.
 */

(function() {

    var displayName;            // <input type=text> with the user name
    var email;                  // <input type=text> with the user email
    var emailValidate           // <input type=button> to validate email
    var emailDelete;            // <input type=button> to remove email
    var feedFormatSelector;     // <select/> used to toggle DEFAULT/COMPACT display format
    var pictureDelete;          // <input type=button> to delete user picture
    var pictureFile;            // <input type=file> to select user picture
    var pictureFileLoading;     // Loading GIF to display while uploading user picture
    var pictureForm;            // <form> containing pictureFile
    var userImage;              // The picture in the side bar
    
    /* This is used to receive a response from iframenull when a user changes
     * user picture, so that I know when it finished uploading.
     */
    window.addEventListener(
        "message",
        function(event) {
            // Do we trust the sender of this message?  (might be
            // different from what we originally opened, for example).
            //if (event.origin !== "http://example.org")
            //    return;
            
            // event.source
            // event.data
            
            switch (event.data.action.toUpperCase())
            {
                // User picture uploaded
                case "UPDATEUSERPICTURE":
                    pictureFileLoading.hide();
                    pictureForm.show();
                    
                    // Reload menu picture on side bar
                    if (event.data.status.toUpperCase() == "DONE")
                        userImage.attr("src", userImage.attr("src") + "?" + Math.random());
                    
                    break;
                default:
                    break;
            }
        },
        false
    );
    
    $(document).ready(function() {
        
        email               = $("#email");
        emailValidate       = $("#validateEmail");
        emailDelete         = $("#deleteEmail");
        displayName         = $("#displayName");
        feedFormatSelector  = $("#feedFormatSelector");
        pictureFile         = $("#pictureFile");
        pictureDelete       = $("#deletePictureFile");
        pictureFileLoading  = $("#pictureFileLoading");
        pictureForm         = $("#pictureForm");
        userImage           = $(".userMenu > .picture > .image");
        
        if (email.val().length)
            emailDelete.show();
        
        // Show "Validate email" button when given new email
        email.keyup(function() {
            if (email.val().length)
                emailValidate.show();
            else
                emailValidate.hide();
        });
        
        // Validate user email
        emailValidate.click(function() {
            var address = email.val();
            
            if (address.length < 1)
                return;
            
            email.prop("disabled", true);
            emailValidate.hide();
            // Hide the email input box and show the message "Check your inbox"
            email.hide().next().show();
            
            var url = Routing.generate("freepost_user_validate_email", {}, true);
            
            $.ajax({
                type:   "post",
                url:    url,
                data:   {
                    email: address
                },
                dataType:	"json"
            })
            .done(function(response) {
            })
            .fail(function(response) {
            });
        });
        
        // Delete user email
        emailDelete.click(function() {
            // URL used to POST the new name
            var url = Routing.generate(
                "freepost_user_delete_email",
                {},
                true
            );
            
            email.prop("disabled", false).val("");
            emailValidate.hide();
            emailDelete.hide();
            
            $.ajax({
                type:   "post",
                url:    url,
                data:   {},
                dataType:	"json"
            })
            .done(function(response) {
            })
            .fail(function(response) {
            });
        });
        
        // Detect a change of feed format
        feedFormatSelector.on("change", function() {
            var feedFormat = $(this).val();
            
            var url = Routing.generate(
                "freepost_user_update_feedformat",
                { feedFormat: feedFormat },
                true
            );
            
            $.ajax({
                type:       "get",
                url:        url,
                data:       {},
                dataType:   "json"
            })
            .done(function(response) {
            })
            .fail(function(response) {
            });
        });
        
        // Submit new picture
        pictureForm.on("submit", function() {
            pictureForm.hide();
            pictureFileLoading.show();
        });
        
        // User selected a new picture
        pictureFile.on("change", function() {
            pictureForm.submit();
        });
        
        // "Delete picture" button clicked
        pictureDelete.click(function(event) {
            if (!confirm("Do you want to delete your picture?"))
            {
                event.preventDefault();
                return;
            }
            
            // Clear input field. Submit again with an empty input to delete the picture.
            pictureForm.trigger("reset").submit();
        });
        
        // Update user display name
        displayName.on("change", function() {
            
            // URL used to POST the new name
            var url = Routing.generate(
                "freepost_user_update_name",
                {},
                true
            );
            
            $.ajax({
                type:   "post",
                url:    url,
                data:   {
                    displayName: displayName.val()
                },
                dataType:	"json"
            })
            .done(function(response) {
            })
            .fail(function(response) {
            });
        });
        
    });
    
})();



