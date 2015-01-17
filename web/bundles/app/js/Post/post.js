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

    var postHashId;             // The hashId of this post
    var postText;               // <div/> containing the post text
    var commentsList;           // <div/> containing the list of comments
    var newComment;             // Form to submit a new comment
    var newCommentEditor;       // CKEDITOR <textarea> for new comment
    var toolbar;                // Toolbar on top of posts
    var toolbarButton;          // Toolbar buttons
    var newCommentSubmit;       // "Submit" button
    var newCommentCancel;       // "Cancel" button
    var loadingGif;             // Loading GIF when submitting post
    var editPost;               // <div/> containing editPostEditor
    var editPostEditor;         // CKEDITOR here to edit post text
    
    // Upvote comment
    var upvote = function(hashId, callback)
    {
        var url = Routing.generate(
            "freepost_comment_upvote",
            {
                commentHashId: hashId
            },
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
            callback(response);
        });
    };
    
    // Downvote comment
    var downvote = function(hashId, callback)
    {
        var url = Routing.generate(
            "freepost_comment_downvote",
            {
                commentHashId: hashId
            },
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
            callback(response);
        });
    };
    
    var showNewCommentForm = function()
    {
        newComment.slideToggle();
        toolbarButton.submit.addClass("selected");
    };

    var hideNewCommentForm = function()
    {
        newComment.slideToggle();
        toolbarButton.submit.removeClass("selected");
    };

    // Choose which options to show in the menu below post text
    var showPostMenu = function(menu)
    {
        toolbarButton.commentsCount.hide();
        toolbarButton.save.hide();
        toolbarButton.edit.hide();
        toolbarButton.cancel.hide();
        toolbarButton.loading.hide();
        
        switch (menu.toUpperCase())
        {
            case "EDIT":
                toolbarButton.save.show();
                toolbarButton.cancel.show();
                break;
            
            default:
                toolbarButton.commentsCount.show();
                toolbarButton.edit.show();
        }
    };
    
    /* Bind a comment buttons events to their handlers
     * 
     * @param aComment: a jQuery object of a $("#comments > .comment") <div> element
     */
    var bindCommentHandlers = function(aComment) {
        
        var hashId          = aComment.data("hashid");
        var userVote        = aComment.data("uservote");
        var text            = aComment.find(".text");
        var button          = {
            cancel:     aComment.find(".menu .cancel"),
            downvote:   aComment.find(".menu .downvote"),
            edit:       aComment.find(".menu .edit"),
            editSave:   aComment.find(".menu .editSave"),
            editCancel: aComment.find(".menu .editCancel"),
            loading:    aComment.find(".menu .loading"),
            points:     aComment.find(".menu .points"),
            reply:      aComment.find(".menu .reply"),
            submit:     aComment.find(".menu .submit"),
            upvote:     aComment.find(".menu .upvote"),
            
            more:       aComment.find(".menu .more")
        };
        var moreButtons     = aComment.find(".menu .moreButtons");
        var replyTextarea   = aComment.find(".replyTextarea");
        var editTextarea    = aComment.find(".editTextarea");
        var replyCkeditor   = null;
                              // Number: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number
        var paddingLeft     = Number(aComment.css("padding-left").replace(/[^-\d\.]/g, ""));
        
        // Show CKEDITOR to send a reply
        var showReplyCkeditor = function() {
            var editorId = "replyEditor" + hashId;
            
            $("<textarea/>", {
                id:     editorId,
                name:   editorId,
                class:  "ckeditor"
            }).appendTo(replyTextarea);
            
            replyCkeditor = CKEDITOR.replace(editorId);
            
            button.cancel.show();
            button.edit.hide();
            button.editSave.hide();
            button.editCancel.hide();
            button.downvote.hide();
            button.loading.hide();
            button.more.hide();
            button.points.hide();
            button.reply.hide();
            button.submit.show();
            button.upvote.hide();
            
            moreButtons.hide();
        };
        var hideReplyCkeditor = function() {
            button.cancel.hide();
            button.edit.show();
            button.editSave.hide();
            button.editCancel.hide();
            button.downvote.show();
            button.loading.hide();
            button.more.show();
            button.points.show();
            button.reply.show();
            button.submit.hide();
            button.upvote.show();
            
            moreButtons.hide();
            
            replyTextarea.empty();
            replyCkeditor = null;
        };
        
        // Show CKEDITOR to edit this comment
        var showEditCkeditor = function() {
            var editorId = "editEditor" + hashId;
            
            $("<textarea/>", {
                id:     editorId,
                name:   editorId,
                class:  "ckeditor"
            }).appendTo(editTextarea);
            
            editCkeditor = CKEDITOR.replace(editorId);
            editCkeditor.setData(text.html());
            
            text.hide();
            
            button.cancel.hide();
            button.edit.hide();
            button.editSave.show();
            button.editCancel.show();
            button.downvote.hide();
            button.loading.hide();
            button.more.hide();
            button.points.hide();
            button.reply.hide();
            button.submit.hide();
            button.upvote.hide();
            
            moreButtons.hide();
        };
        var hideEditCkeditor = function() {
            button.cancel.hide();
            button.edit.show();
            button.editSave.hide();
            button.editCancel.hide();
            button.downvote.show();
            button.loading.hide();
            button.more.show();
            button.points.show();
            button.reply.show();
            button.submit.hide();
            button.upvote.show();
            
            moreButtons.hide();
            
            text.show();
            
            editTextarea.empty();
            editCkeditor = null;
        };
        
        button.upvote.click(function(event) {
            var commentVote = parseInt(button.points.text());
            
            // Already upvoted
            if (userVote == 1)
            {
                button.points.text(commentVote - 1);
                
                // Set this comment as not voted
                userVote = 0;
                
                button.upvote.removeClass("selected");
            }
            // Upvote. +2 if downvoted earlier
            else
            {
                button.points.text(commentVote + (userVote == 0 ? 1 : 2));
            
                // Set this comment as upvoted
                userVote = 1;
                
                button.downvote.removeClass("selected");
                button.upvote.addClass("selected");
            }
            
            upvote(hashId, function(response) {
                if (response.done)
                {
                }
            });
        });
        
        button.downvote.click(function(event) {
            var commentVote = parseInt(button.points.text());
            
            // Already downvoted
            if (userVote == -1)
            {
                button.points.text(commentVote + 1);
                
                // Set this comment as not voted
                userVote = 0;
                
                button.downvote.removeClass("selected");
            }
            // Downvote. -2 if upvoted earlier
            else
            {
                button.points.text(commentVote - (userVote == 0 ? 1 : 2));
            
                // Set this comment as downvoted
                userVote = -1;
                
                button.upvote.removeClass("selected");
                button.downvote.addClass("selected");
            }
            
            downvote(hashId, function(response) {
                if (response.done)
                {
                }
            });
        });
        
        // Write a new comment reply
        button.reply.click(function() {
            showReplyCkeditor();
        });
        
        // Submit comment reply
        button.submit.click(function() {
            var text  = replyCkeditor.getData();
            
            if (text.length < 1) return;
            
            // URL used to POST the new post
            var url             = Routing.generate(
                "freepost_comment_submit_new",    // route
                {                           // route params
                    postHashId: postHashId
                },
                true                        // absolute URL
            );
            
            button.submit.hide();
            button.loading.show();
            
            // Submit new post
            $.ajax({
                type:   "post",
                url:    url,
                data:   {
                    parentHashId:   hashId,
                    text:           text
                },
                dataType:	"json"
            })
            .done(function(response) {
                replyCkeditor.setData("");
                hideReplyCkeditor();
                
                // If the reply has been posted
                if (response.hasOwnProperty("done") && response.done)
                {
                    var newReply = $(response.html);
                    
                    // Add the new reply to the page
                    newReply.css("padding-left", (paddingLeft+32)+"px").insertAfter(aComment);
                    
                    // Bind menu buttons handlers
                    bindCommentHandlers(newReply);
                }
            })
            .fail(function(response) {
                button.submit.show();
            })
            .always(function(response) {
                button.loading.hide();
            });
        });
        
        // Cancel comment reply
        button.cancel.click(function() {
            hideReplyCkeditor();
        });
        
        // Edit my comment
        button.edit.click(function() {
            showEditCkeditor();
        });
        
        // Save comment edit
        button.editSave.click(function() {
            var newCommentText  = editCkeditor.getData();
            
            if (newCommentText.length < 1) return;
            
            var url = Routing.generate(
                "freepost_comment_edit",
                { commentHashId: hashId },
                true
            );
            
            button.editSave.hide();
            button.loading.show();
            
            // Submit new post edits
            $.ajax({
                type:   "post",
                url:    url,
                data:   {
                    text: newCommentText
                },
                dataType:	"json"
            })
            .done(function(response) {
                editCkeditor.setData("");
                hideEditCkeditor();
                text.html(newCommentText);
            })
            .fail(function(response) {
                button.editSave.show();
            })
            .always(function(response) {
                button.loading.hide();
            });
        });
        
        // Cancel comment edit
        button.editCancel.click(function() {
            hideEditCkeditor();
        });
        
        // Display more options
        button.more.click(function() {
            /* Description: Bind two or more handlers to the matched elements,
             * to be executed on alternate clicks.
             * https://api.jquery.com/toggle-event/
            */
            /*
            moreButtons.toggle(
                function() {
                    moreButtons.animate({
                        height: "show",
                        width:  "hide"
                    }, 'slow');
                },
                function() {
                    moreButtons.animate({
                        height: "show",
                        width:  "show"
                    }, 'slow');
                },
                function() {
                    alert("ciao");
                }
            );
            */
            moreButtons.animate({width: "toggle"});
        });
    };
    
    $(document).ready(function() {
        
        postHashId          = $("#newComment input[name=postHashId]").val();
        postText            = $(".post > .content > .text");
        commentsList        = $("#comments");
        newComment          = $("#newComment");
        newCommentSubmit    = $("#newComment input[type=submit]");
        newCommentCancel    = $("#newComment > .menu > .cancel");
        loadingGif          = $("#newComment #loading");
        editPost            = $(".post > .content > .editPost");
        toolbar             = $("#toolbar");
        toolbarButton       = {
            commentsCount:  $(".post > .content > .menu > .commentsCount"),  // Show the number of comments here
            edit:           $(".post > .content > .menu > .edit"),           // Edit post
            save:           $(".post > .content > .menu > .save"),           // Save post edits
            cancel:         $(".post > .content > .menu > .cancel"),         // Cancel post edits
            loading:        $(".post > .content > .menu > .loading"),        // Loading icon to show when Saving new post text
            submit:         $("#toolbar > #submit")                          // Submit new comment
        };
        
        // Pointer to the editor
        CKEDITOR.on('instanceReady', function(evt) {
            switch (evt.editor.name)
            {
                case "editPostEditor":
                    editPostEditor = evt.editor;
                    break;
                case "newCommentEditor":
                    newCommentEditor = evt.editor;
                    break;
            }
        });
        
        // Button to show/hide new comment form
        toolbarButton.submit.click(function() {
            newComment.is(":visible") ? hideNewCommentForm() : showNewCommentForm();
        });
        
        // Button to edit post text
        toolbarButton.edit.click(function() {
            postText.hide();
            editPostEditor.setData(postText.html());
            editPost.show();
            
            showPostMenu("edit");
        });
        
        // Button to cancel post text
        toolbarButton.cancel.click(function() {
            editPost.hide();
            postText.show();
            editPostEditor.setData("");
            
            showPostMenu("default");
        });
        
        // Button to save post text
        toolbarButton.save.click(function() {
            var newPostText  = editPostEditor.getData();
            
            if (newPostText.length < 1) return;
            
            var url = Routing.generate(
                "freepost_post_edit",
                { postHashId: postHashId },
                true
            );
            
            toolbarButton.save.hide();
            toolbarButton.loading.show();
            
            // Submit new post edits
            $.ajax({
                type:   "post",
                url:    url,
                data:   {
                    text: newPostText
                },
                dataType:	"json"
            })
            .done(function(response) {
                editPostEditor.setData("");
                editPost.hide();
                postText.html(newPostText).show();
                
                showPostMenu("default");
            })
            .fail(function(response) {
                toolbarButton.save.show();
            })
            .always(function(response) {
                toolbarButton.loading.hide();
            });
        });
        
        // Submit new comment
        newCommentSubmit.click(function() {
            
            var text  = newCommentEditor.getData();
            
            if (text.length < 1)
                return;
            
            // URL used to POST the new comment
            var url = Routing.generate(
                "freepost_comment_submit_new",    // route
                {                           // route params
                    postHashId: postHashId
                },
                true                        // absolute URL
            );
            
            newCommentSubmit.hide();
            loadingGif.show();
            
            // Submit new comment
            $.ajax({
                type:   "post",
                url:    url,
                data:   {
                    text:   text
                },
                dataType:	"json"
            })
            .done(function(response) {
                hideNewCommentForm();
                
                newCommentEditor.setData("");
                
                // If the comment has been posted
                if (response.hasOwnProperty("done") && response.done)
                {
                    var newComment = $(response.html);
                    
                    // Add the new comment to the page
                    commentsList.prepend(newComment);
                    
                    // Bind menu buttons handlers
                    bindCommentHandlers(newComment);
                }
            })
            .fail(function(response) {
            })
            .always(function(response) {
                loadingGif.hide();
                newCommentSubmit.show();
            });
        });
        
        newCommentCancel.click(function() {
            hideNewCommentForm();
            newCommentEditor.setData("");
        });
        
        // Loop comments
        $("#comments > .comment").each(function(index, aComment) {
            
            aComment = $(aComment);
            bindCommentHandlers(aComment);
            
        });
        
    });
    
})();


















