function mozillaPersonaSetup(currentUser)
{
    navigator.id.watch({
      loggedInUser: currentUser,
      onlogin: function(assertion) {
        $.ajax({
          type: 'POST',
          dataType: 'json',
          url: path + "/AJAX/JSON_PersonaLogin?method=login",
          data: {
            assertion: assertion
          },
          success: function(response, status, xhr) { 
            if (response.status == "OK") { 
              window.location.reload();
            } else {
              alert("Login failed");
            }
          },
          error: function(xhr, status, err) { alert("login failure: " + err); }
        });
      },
      onlogout: function() {
        $.ajax({
          type: 'GET',
          dataType: 'json',
          url: path + "/AJAX/JSON_PersonaLogin?method=logout",
          success: function(response, status, xhr) { if (window.location.href != path) window.location = path; else window.location.reload() },
          error: function(xhr, status, err) { alert("logout failure: " + err); }
        });
      }
    });
    
    var signinLink = document.getElementById('personaLogin');
    if (signinLink) {
      signinLink.onclick = function() { navigator.id.request(); return false; };
    }
    var signoutLink = document.getElementById('personaLogout');
    if (signoutLink) {
      signoutLink.onclick = function() { navigator.id.logout(); return false; };
    }    
}