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

    var notification = {
        newReplies: null    // "You have $1 new replies" <a/> messages in user menu
    };
    
    var checkUnreadReplies = function() {
        var url = Routing.generate(
            "freepost_user_unread_replies",
            {},
            true
        );
        
        $.ajax({
            type:   "get",
            url:    url,
            data:   {},
            dataType:	"json"
        })
        .done(function(response) {
            if (response.hasOwnProperty("count") && response.count > 0)
                notification.newReplies
                    .text("You have " + response.count + " new " + (response.count == 1 ? "reply" : "replies"))
                    .css("display", "block");
        })
        .fail(function(response) {
        });
    };
    
    $(document).ready(function() {
        // This is defined in "views/Default/User/menu.html.twig"
        notification.newReplies = $(".userMenu .newReplies");
        
        // Check new unread replies
        checkUnreadReplies();
        
        // Schedule task to check for new unread replies every 2 minutes
        setInterval(checkUnreadReplies, 120000);
    });
    
})();










