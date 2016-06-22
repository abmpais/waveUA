//Transform JSON into string
Template7.registerHelper('stringify', function (context){
    var str = JSON.stringify(context);
    return str.replace(/'/g, '&#39;');
});

//Check if player has source (localStorage)
var expand = false;
var mediaStorage = window.localStorage;
var playerHtml = document.getElementById("player");
navigator.geolocation.watchPosition(app.onSuccess, app.onError);
var spotifyLink = document.getElementById("spotifyLink");
//If player has no source do nothing
if (mediaStorage.getItem("playerUrl")===null){
}
else {
    playerHtml.setAttribute("src",mediaStorage.getItem("playerUrl"));
    spotifyLink.setAttribute("href",mediaStorage.getItem("playerExt"));

}

// Initialize your app
var myApp = new Framework7({
    precompileTemplates: true,
    template7Pages: true,
    modalTitle: "WaveUA"
});

// Export selectors engine (jQuery like)
var $$ = Dom7;

// Add views
var mainView = myApp.addView('.view-main', {
    dynamicNavbar: true,
    domCache: true
});
// Handle Range Slider
// - This function displays the value next to the slider as it slides for better visual indicator
$$(document).on('input change', 'input[type="range"]', function (e) {
    $$('input#sliderVal').val(this.value);
});

// Handle the Cordova deviceready Event
$$(document).on('deviceready', function() {
    navigator.geolocation.watchPosition(app.onSuccess, app.onError);
});

// Handle Submit Button
// - This function calls the Spotify Web API with the designated search options then loads the list
// page when a response is received.
$$(document).on('click', '#btnSearch', function (e) {
    var term = $$("#term").val();
    if (term.length==0) {
        myApp.alert("Please enter a search term.");
    }
    else {
        var mediaType = $("#searchTerms").val();
        var numResults = $$("#numResults").val();

        var url = "https://api.spotify.com/v1/search?q=" + term +"&type=" + mediaType + "&limit=" + numResults;
        //different types of search (track/artist/album)
        if (mediaType=="track") {
            $$.ajax({
                dataType: 'json',
                url: url,
                success: function (resp) {
                    mainView.router.load({
                        template: Template7.templates.listTemplate,
                        context: resp.tracks.items
                    });
                },
                error: function (xhr) {
                    console.log("Error on ajax call " + xhr);
                }
            });
        }
        else if (mediaType=="artist"){
            $$.ajax({
                dataType: 'json',
                url: url,
                success: function (resp) {
                    mainView.router.load({
                        template: Template7.templates.artistTemplate,
                        context: resp.artists.items
                    });
                },
                error: function (xhr) {
                    console.log("Error on ajax call " + xhr);
                }
            });
        }
        else {
            $$.ajax({
                dataType: 'json',
                url: url,
                success: function (resp) {
                    mainView.router.load({
                        template: Template7.templates.albumTemplate,
                        context: resp.albums.items
                    });
                },
                error: function (xhr) {
                    console.log("Error on ajax call " + xhr);
                }
            });
        }
    }


});

// Media List Page Handling
//FAVOURITE/PLAYLIST SLIDE FUNCTION
myApp.onPageInit('list', function (page) {
    $$(page.container).find('.favorite').on('click', function (e) {
        // this.dataset.item returns current index of item clicked so we can retrieve from context
        var item = page.context[this.dataset.item];
        myApp.alert(item.name + ' added to favorites!');
    });
//PREVIEW SLIDE FUNCTION
    $$(page.container).find('.preview').on('click', function (e) {
        var item = page.context[this.dataset.item];
        mediaStorage.setItem("playerUrl",item.preview_url);
        mediaStorage.setItem("playerExt",item.external_urls.spotify);
        playerChange();
    });

//SHARE SLIDE FUNCTION
    $$(page.container).find('.share').on('click', function (e) {
        var item = page.context[this.dataset.item];

        if (window.plugins && window.plugins.socialsharing) {
            window.plugins.socialsharing.share("Hey! My new favorite song is " + item.name + " check it out!",
                'Check this out', item.album.images[2].url, item.preview_url,
                function () {
                    console.log("Share Success")
                },
                function (error) {
                    console.log("Share fail " + error)
                });
        }
        else myApp.alert("Share plugin not found");
    });
});
// Artist Item Page Handling
myApp.onPageInit('artist', function (page) {
});
// Media Item Page Handling
myApp.onPageInit('media', function (page) {
    var item = page.context;

//Event Listener for Player Music Change
    document.getElementById("playerBtn").addEventListener("click",playerUpdate);
    document.getElementById("playerBtn").addEventListener("click",playerChange);
//Changes the player info (only called on user click (play))
    function playerUpdate(){
        mediaStorage.setItem("playerUrl",item.preview_url);
        mediaStorage.setItem("playerTrack",item.name);
        mediaStorage.setItem("playerArtist",item.artists[0].name);
        mediaStorage.setItem("playerExt",item.external_urls.spotify);
        mediaStorage.setItem("playerImg",item.album.images[2].url);
        mediaStorage.setItem("playerImgBig",item.album.images[0].url);
        mediaStorage.setItem("playerID",item.id);
        mediaStorage.setItem("playerAlbum",item.album.name);
    }
    //Updates the player info (only called on "play" inside a search)
    function playerChange (){
        playerHtml.pause();
        playerHtml.setAttribute("src",mediaStorage.getItem("playerUrl"));
        spotifyLink.setAttribute("href",mediaStorage.getItem("playerExt"));

        playerHtml.play();
    }

    $$(page.container).find('.share').on('click', function (e) {
        if (window.plugins && window.plugins.socialsharing) {
            window.plugins.socialsharing.share("Hey! My new favorite song is " + item.name + " check it out!",
                'Check this out', item.album.images[2].url, item.preview_url,
                function () {
                    console.log("Share Success")
                },
                function (error) {
                    console.log("Share fail " + error)
                });
        }
        else myApp.alert("Share plugin not found");
    });
    $$(page.container).find('.favorite').on('click', function (e) {
        myApp.alert(item.name + ' added to favorites!');
    });
    $$(page.container).find('.preview').on('click', function (e) {
        mediaStorage.setItem("playerUrl",item.preview_url);
        mediaStorage.setItem("playerExt",item.external_urls.spotify);
        playerChange();
    });
});


// Side Menu Handlers
$$(document).on('click', '#about', function (e) {
    myApp.alert('Show About');
});
$$(document).on('click', '#mapMenu', function(e){
    mainView.router.load({pageName: 'map'});
    navigator.geolocation.watchPosition(app.onSuccess, app.onError);

});
$$(document).on('click', '#feedMenu', function(e){
    $("#feedLi").empty();
    $.getJSON("http://wave.web.ua.pt/www/db/feed.php?idUser="+mediaStorage.getItem("idUser"),function(result){
        $.each(result, function(i, field){
            $("#feedLi").append("<li class='swipeout'>"+
                "<div class='swipeout-content'>"+
                "<div class='item-media'>"+
                "<img src='"+field.foto+"' style=' border-radius: 250px; width: 7%' class='lazy'>"+
                "</div>"+
                "<div class='item-inner'>"+
                "<div class='item-text'>"+field.nameUser+"</div>"+
                "<div class='item-text' style='position: absolute; left:0%; bottom: -25%'>"+field.datePosts+"</div>"+
                "<div class='item-title-row'>"+
                "<div class='item-title'>"+field.nameMusics+"</div>"+
                "</div>"+
                "<div class='item-subtitle'>"+field.artist+"</div>"+
                "</div>"+
                "</div>"+
                "</li>");
        });
    });
    mainView.router.load({pageName: 'feed'});


});
$$(document).on('click', '#indexMenu', function(e){
    mainView.router.load({pageName: 'index'});
});
$$(document).on('click', '#searchIcon', function(e){
    mainView.router.load({pageName: 'index'});
});

$$(document).on('click', '#settingsMenu', function(e){
    mainView.router.load({pageName: 'settings'});
    $(document).ready(function() {
        $.getJSON("http://wave.web.ua.pt/www/db/json.php",function(result){
            $.each(result, function(i, field){
                $("#dbDisplay").append(field.album + "<br/>");
            });
        });
    });

});
$$(document).on('click', '#profileMenu', function(e){
    mainView.router.load({pageName: 'profile'});

});
//ON PLAYER BUTTONS
$$(document).on('click', '#expandPlayerBack', function(e){
        expand=false;
        $("#expandIcon").removeClass("fa fa-minus");
        $("#expandIcon").addClass("fa fa-arrows-alt");
});
$$(document).on('click', '#btn-expand', function(e){
    if (expand==false){
        expand=true;
        $("#expandIcon").removeClass("fa fa-arrows-alt");
        $("#expandIcon").addClass("fa fa-minus");
        $("#expandContent").css("background-image", "url("+mediaStorage.getItem('playerImgBig')+")");
        mainView.router.load({pageName: 'expandPlayer'});
    }
    else {
        expand=false;
        $("#expandIcon").removeClass("fa fa-minus");
        $("#expandIcon").addClass("fa fa-arrows-alt");
        mainView.router.back();
    }
});
//PLAYER ACTION SHEET
$$('.ac-1').on('click', function () {
    var buttons = [
        {
            text: 'Post Now',
            bold: true,
            onClick: function (){
                $.ajax({
                    type:"GET",
                    url: "db/insertPlaylist.php",
                    data: "id=" + mediaStorage.getItem("playerID") + "&idUser=" + mediaStorage.getItem("idUser") +
                    "&name=" + mediaStorage.getItem("playerTrack")+ "&artist=" + mediaStorage.getItem("playerArtist")+
                    "&album=" + mediaStorage.getItem("playerAlbum")+
                    "&link=" + mediaStorage.getItem("playerUrl"),
                    dataType: "json",
                    success: function(data){
                        window.alert("Post successful!")
                    }

                });
            }
        },
        {
            text: 'Share'
        },
        {
            text: 'Cancel',
            color: 'red'
        }
    ];
    myApp.actions(buttons);
});
//ON PAGE LOADINGS:

// DATABASE
// MEDIA PLAYLISTS
myApp.onPageInit ('media', function (page) {
    $(document).ready(function() {
        $.getJSON("http://wave.web.ua.pt/www/db/json.php",function(result){
            $.each(result, function(i, field){
                $("#playlist").append("<option value= ''>"+field.nameMusics+"</option>");
            });
        });
    });
});

// MAP AND GEOLOCATION
var app =  {
    // Application Constructor
    onSuccess: function (position){
        var longitude = position.coords.longitude;
        var latitude = position.coords.latitude;
        var latLong = new google.maps.LatLng(latitude, longitude);

        var mapOptions = {
            center: latLong,
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

        var marker = new google.maps.Marker({
            position: latLong,
            map: map,
            title: 'my location'
        });
    },

    onError: function(error){
        alert("the code is " + error.code + ". \n" + "message: " + error.message);
    },
};



//OPEN WINDOWS IN POPUP (GOOD FOR DATABASE STUFF)
function popupform(myform, windowname)
{
    if (! window.focus)return true;
    window.open('', windowname, 'height=200,width=400,scrollbars=no');
    myform.target=windowname;
    return true;
}

// Player
$(function() {

    var audio = $("#player")[0];
    var progressBar = $("#progress-bar");

    $('#btn-play-pause, #btn-play-pauseExp, #playerBtn').on('click', function() {
        //Play/pause the track
        if (audio.paused == false) {
            audio.pause();
            $(this).children('i').removeClass('fa-pause');
            $(this).children('i').addClass('fa-play');
            $('#btn-play-pauseExp,#playerBtn').children('i').removeClass('fa-pause');
            $('#btn-play-pauseExp,#playerBtn').children('i').addClass('fa-play');
        } else {
            audio.play();
            $(this).children('i').removeClass('fa-play');
            $(this).children('i').addClass('fa-pause');
            $('#btn-play-pauseExp,#playerBtn').children('i').removeClass('fa-play');
            $('#btn-play-pauseExp,#playerBtn').children('i').addClass('fa-pause');
        }
    });


    $('#btn-stop,#btn-stopExp,#playerBtn').on('click', function() {
        //Stop the track
        audio.pause();
        audio.currentTime = 0;
        $('#btn-play-pause,#btn-play-pauseExp,#playerBtn').children('i').removeClass('fa-pause');
        $('#btn-play-pause,#btn-play-pauseExp,#playerBtn').children('i').addClass('fa-play');
    });

    $('#btn-mute,#btn-muteExp').on('click', function() {
        //Mutes/unmutes the sound
        if(audio.volume != 0) {
            audio.volume = 0;
            $(this).children('i').removeClass('fa-volume-up');
            $('#btn-muteExp').children('i').removeClass('fa-volume-up');
            $(this).children('i').addClass('fa-volume-off');
            $('#btn-muteExp').children('i').addClass('fa-volume-off');
        } else {
            audio.volume = 1;
            $(this).children('i').removeClass('fa-volume-off');
            $('#btn-muteExp').children('i').removeClass('fa-volume-off');
            $(this).children('i').addClass('fa-volume-up');
            $('#btn-muteExp').children('i').addClass('fa-volume-up');
        }
    });
    //Progress Bar event listener
    $('#player').on('timeupdate', function() {
        $('#progress-bar').attr("value", this.currentTime / this.duration);
    });
});




