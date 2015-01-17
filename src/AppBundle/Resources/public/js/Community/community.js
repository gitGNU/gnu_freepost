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

    var image;
    var followButton;
    var isFollowing = function(callback) {
        var url = Routing.generate(
            "freepost_user_follows_community",
            { communityHashId: followButton.data("community-hashid") },
            true
        );
        
        $.ajax({
            type:   "post",
            url:    url,
            data:   {},
            dataType:	"json"
        })
        .done(function(response) {
            callback(response && response.follows);
        })
        .fail(function(response) {
            callback(false);
        });
    };
    
    $(document).ready(function() {
        
        image        = $(".communityMenu > .picture > .image");
        followButton = $("#follow");
        
        // If user is signed in and we have a "Follow" button
        if (followButton.length)
        {
            followButton.click(function() {
                
                followButton.hide();
                
                var url = Routing.generate(
                    "freepost_community_follow",
                    { communityHashId: followButton.data("community-hashid") },
                    true
                );
                
                $.ajax({
                    type:   "post",
                    url:    url,
                    data:   {},
                    dataType:	"json"
                })
                .done(function(response) {
                })
                .fail(function(response) {
                })
                .always(function(response) {
                });
            });
            
            // Show "Follow" button
            isFollowing(function(follows) {
                !follows && followButton.css("visibility", "visible");
            });
        }
    });
    
})();