;(function($){


    $.fn.libreplayer = function(options) {
        
        var settings = $.extend({
            
            currentClass:'current',
            playingClass:'playing',

            controls: {
                stop:'.stop',
                play:'.play',
                pause:'.pause',
                previous:'.previous',
                next:'.next',
                currentTime:'.current-time',
                totalTime:'.total-time',
                track:'.track',
            }
            
            },options);
        
        var player = $(this),
            
            ui = {
                
                stop : player.find(settings.controls.stop),
                play : player.find(settings.controls.play),
                pause : player.find(settings.controls.pause),
                previous : player.find(settings.controls.previous),
                next : player.find(settings.controls.next),
                currentTime : player.find(settings.controls.currentTime),
                totalTime : player.find(settings.controls.totalTime)
            },
            
            
            tracks = [],
            ready = 0;
            
            player.totalTime = 0;
            player.currentTrack = false;
            
        // Build playlist
        player.find(settings.controls.track).each(function(i){
            tracks[i+1] = {
                html: $(this),
                audio:$(this).find('audio')[0],
                duration:$(this).find('audio')[0].duration,
            }

            tracks[i+1].audio.addEventListener('timeupdate',function (){
                player.updateTime(this.currentTime,this.duration);
            });
            
            //causes 'next' to happen when end of buffer is reached, used simple comparison at updateTime() call instead.  
            //tracks[i+1].audio.addEventListener('ended',function (){
            //    player.nav(true);
            //});
            
 
            tracks[i+1].audio.addEventListener('durationchange',function (){
                if (player.currentTrack) {
                    player.trigger('totalTimeChanged',tracks[player.currentTrack].audio.duration)
                }
            });  
            
            tracks[i+1].html.click(function(e){
                e.preventDefault();
                //if (player.currentTrack!=i+1) {
                    player.setTrack(i+1);
                //}
            })
            tracks[i+1].html.find('audio').hide();
        
            
        });
        
        // Player's functions
        
        player.setTrack = function(index) {
            
            if (player.currentTrack) {
                player.setPlay(false);
                tracks[player.currentTrack].audio.currentTime = 0;
                tracks[player.currentTrack].html.removeClass(settings.currentClass);
            }
            player.currentTrack = index;
            
            if (index) {
                player.setPlay(true);
                tracks[player.currentTrack].html.addClass(settings.currentClass);
                player.trigger('totalTimeChanged',tracks[player.currentTrack].audio.duration);
                tracks[player.currentTrack].html.focus().blur();
            } else {
                player.trigger('totalTimeChanged',player.totalTime)
            }
        }
        
        player.setPlay = function(play) {
            if (!player.currentTrack) {
                player.setTrack(1);
            }
            if (play) {
                tracks[player.currentTrack].audio.play();
                player.addClass(settings.playingClass);
            } else {
                tracks[player.currentTrack].audio.pause();
                player.removeClass(settings.playingClass);
            }
            
        }
        
        player.nav = function(direction) {
            if (player.currentTrack) {

                if (direction && tracks[player.currentTrack+1]) {
                    player.setTrack(player.currentTrack+1)
                } else if (!direction && tracks[player.currentTrack-1]) {
                    player.setTrack(player.currentTrack-1)
                } else {
                    player.reset();
                } 
            }
        
        }
        
        player.reset = function() {
            player.setTrack(false);
            setTimeout(function(){
                player.updateTime(0,player.totalTime)
            },100)
        }
        
        player.setCurrentTime = function(current) {
            if (player.currentTrack) {
                tracks[player.currentTrack].audio.currentTime = current;
            }
        }
        
        player.updateTime = function(current,total) {
            ui.currentTime.html(timeFormat(current));
            ui.totalTime.html(timeFormat(total));
            player.trigger('currentTimeChanged',current);
            if (current==total) {
                player.nav(true);
            }
        }
        
        player.setVolume = function(v) {
            $(tracks).each(function(i){
                if (i<tracks.length-1) {
                    tracks[i+1].audio.volume = v;
                }
            })
        }
        
        var timeFormat = function(secs) {
            var hr  = Math.floor(secs / 3600),
                min = Math.floor((secs - (hr * 3600))/60),
                sec = Math.floor(secs - (hr * 3600) -  (min * 60));
            if (min < 10){min = "0" + min;}
            if (sec < 10){sec  = "0" + sec;}
            return min + ':' + sec;
        }
        
        
        // Gui's events
        
        ui.play.click(function(e){e.preventDefault();player.setPlay(true)});
        ui.pause.click(function(e){e.preventDefault();player.setPlay(false)});
        ui.previous.click(function(e){e.preventDefault();player.nav(false)});
        ui.next.click(function(e){e.preventDefault();player.nav(true)});
        ui.stop.click(function(e){e.preventDefault();player.reset()});
        
        

        

        var oggTestAudio = new Audio();
        var canPlayOgg = !!oggTestAudio.canPlayType && oggTestAudio.canPlayType('audio/ogg; codecs="vorbis"') != "";
        
        if (!canPlayOgg) {
            player.addClass('disabled');
        }

        return player;
        


    }


}(jQuery));
