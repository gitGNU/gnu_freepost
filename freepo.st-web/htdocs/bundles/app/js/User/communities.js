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

    var communities;        // List of communities
    var communitiesSubscribed; // List of subscribed communities
    var communitiesItems;   // <div> within communities
    var list;               // Load posts in here
    var allCommunities;     // The special menu item "All communities" used to show all communities
    var search = {          // Search input box
        filter: null,
        results: null,
        
        request: null       /* Keep a reference to the last ajax search request.
                             * This is useful when a user types a word in the search
                             * input box, so that only the last one returns the desired
                             * html code.
                             */
    };
    
    // When clicking a community on the left bar
    var loadCommunityPosts = function(communityHashId) {
        var url = Routing.generate(
            "freepost_user_read_community_posts",
            {
                communityHashId: communityHashId
            },
            true
        );
        
        $.ajax({
            type:   "post",
            url:    url,
            data:   {},
            dataType:	"html"
        })
        .done(function(response) {
            list.html(response);
        })
        .fail(function(response) {
        });
    };
    
    // "All communities" button
    var loadCommunitiesList = function() {
        communitiesItems.removeClass("selected");
        allCommunities.addClass("selected");
        
        var url = Routing.generate("freepost_user_search_communities", {}, true);
        
        $.ajax({
            type:       "get",
            url:        url,
            data:       {},
            dataType:	"json"
        })
        .done(function(response) {
            response.html && list.html(response.html);
        })
        .fail(function(response) {
        });
    };
    
    var setCommunitiesItemsClickHandler = function() {
        // Click event for each community
        communities.find(".community").each(function(index, aCommunity) {
            aCommunity = $(aCommunity);
            
            aCommunity.click(function() {
                loadCommunityPosts(aCommunity.data("hashid"));
                
                allCommunities.removeClass("selected");
                communitiesItems.removeClass("selected");
                aCommunity.addClass("selected");
            });
        });
    };
    
    $(document).ready(function() {
        
        communities             = $("#communities");
        communitiesSubscribed   = $("#communities > .subscribed");
        communitiesItems        = communities.find(".community");
        list                    = $("#list");
        search.filter           = $("#communities > .search > input[name=filter]");
        search.results          = $("#communities > .search > .results");
        allCommunities          = communities.find(".allCommunities");
        
        setCommunitiesItemsClickHandler();
        
        // Select the first community
        communitiesItems.length && communitiesItems.first().click();
        
        // Start searching when user types something
        search.filter.keyup(function() {
            // Kill the last search request
            search.request && search.request.abort();
            
            var name = search.filter.val();
            
            if (name.length < 1)
            {
                search.results.html("").hide();
                communitiesSubscribed.show();
                return;
            }
            
            var url             = Routing.generate(
                "freepost_community_search",
                { communityName: name },
                true
            );
            
            search.request = $.ajax({
                type:   "get",
                url:    url,
                data:   {},
                dataType:	"json"
            })
            .done(function(response) {
                if (response.hasOwnProperty("html"))
                {
                    search.results.html(response.html).show();
                    communitiesSubscribed.hide();
                } else
                {
                    search.results.html("").hide();
                    communitiesSubscribed.show();
                }
                
                setCommunitiesItemsClickHandler();
            })
            .fail(function(response) {
            })
            .always(function(response) {
            });
        });
        
        allCommunities.click(function() {
            loadCommunitiesList();
        });
    });
    
})();










