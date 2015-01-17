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

    var menu = {
        signin:        null,
        signup:        null,
        resetPassword: null
    };
    var form = {
        signin:        null,
        signup:        null,
        resetPassword: null,
        
        input: {
            signup: {
                username: null,
                password: null
            }
        },
        
        message: {
            validUsername: null,
            badUsername: null
        }
    };
    
    var selectForm = function(formName) {
        $(".menu > li").removeClass("selected");
        $(".form > div").removeClass("selected");
        
        switch (formName.toUpperCase())
        {
            case "SIGNUP":
                menu.signup.addClass("selected");
                form.signup.addClass("selected");
                break;
            case "RESETPASSWORD":
                menu.resetPassword.addClass("selected");
                form.resetPassword.addClass("selected");
                break;
            default:
                menu.signin.addClass("selected");
                form.signin.addClass("selected");
        };
    };
    
    var checkUsername = function(username, callback) {
        var url = Routing.generate(
            "freepost_user_check_username",    // route
            {                           // route params
                userName: username
            },
            true                        // absolute URL
        );
        
        $.ajax({
            type:   "get",
            url:    url,
            data:   {},
            dataType:	"json"
        })
        .done(function(response) {
            callback && callback(response.exists);
        })
        .fail(function(response) {
        })
        .always(function(response) {
        });
    };
    
    $(document).ready(function() {
        
        menu.signin         = $(".menu > .signin");
        menu.signup         = $(".menu > .signup");
        menu.resetPassword  = $(".menu > .resetPassword");
        
        form.signin         = $(".form > .signin");
        form.signup         = $(".form > .signup");
        form.resetPassword  = $(".form > .resetPassword");
        
        form.input.signup.username = $(".form > .signup input[name=username]");
        form.input.signup.password = $(".form > .signup input[name=password]");
        
        form.message.validUsername = $(".form > .signup .validUsername");
        form.message.badUsername   = $(".form > .signup .badUsername");
        
        menu.signin.click(function() {
            selectForm("signin");
        });
        
        menu.signup.click(function() {
            selectForm("signup");
        });
        
        menu.resetPassword.click(function() {
            selectForm("resetPassword");
        });
        
        // Check username availability
        form.input.signup.username.change(function() {
            var username = form.input.signup.username.val();
            
            if (username.length < 5)
                return;
            
            form.message.validUsername.hide();
            form.message.badUsername.hide();
            
            checkUsername(
                username,
                function(exists) {
                    exists ? form.message.badUsername.show() : form.message.validUsername.show();
                }
            );
        });
    });
    
})();










