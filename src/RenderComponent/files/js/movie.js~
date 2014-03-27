   var CamCorderView = AppView.extend({
        events: {
           "click .camcorder-movie-rec-start" : "startRecord",
           "click .camcorder-movie-rec-stop" : "stopRecord",
           "click .camcorder-movie-play" : "playMovie",
           "click .camcorder-movie-stop" : "stopMovie",
           "click .camcorder-webcam-parameter" : "webcamParameters",
           "click .camcorder-show button" : "show"
        },
       initialize: function()
       {
           Backbone.Events.on("camcorder.start", this.onCamcorderStart.bind(this));
           Backbone.Events.on("camcorder.stop", this.onCamcorderStop.bind(this));
           Backbone.Events.on("camcorder.timeupdated", this.onCamcorderTimeupdated.bind(this));
           Backbone.Events.on("camcorder.moviestoped", this.onCamcorderMovieStoped.bind(this));
           Backbone.Events.on("camcorder.onload", this.onCamcorderOnload.bind(this));

           this.cacheActions = [];

           this.videoid = "video_" + uniqid();
       },
       initializeUploader : function()
       {
           var addDocumentList = this.addDocumentList;

           require(["jquery.fileupload"], (function() {

               this.$el.find("#camcorder").fileupload({
                   dataType: 'json',
                   add: (function(e, data)
                   {
                       this.$el.find(".btn.record").button("loading");

                       data.submit();
                   }).bind(this),
                   progress: this.onProgressUpload.bind(this),
                   done: (function(e, data) {
                       this.onMovieUploaded(data.result);
                       this.$el.find(".btn.record").button("reset");
                       this.$el.find(".btn.record").after("<img src='/" + data.result.webpicture + "'/>");

                   }).bind(this)
               });

           }).bind(this));


       },
       onProgressUpload: function(data)
       {
          var progress = parseInt(data.loaded / data.total * 100, 10);
          var $btn = this.$el.find(".btn.record");
          $btn.text($btn.data("loadingText") + progress + "%");
       },
       onCamcorderOnload: function(videoid)
       {
           _.each(this.cacheActions, function(action, index, list)
           {
                this[action].apply(this, []);
           }, this);

           this.cacheActions = [];
       },
       onCamcorderTimeupdated: function(parameters)
       {
            this.$el.find(".timeleft").text(parameters.time);
       },
       onMovieUploaded: function(result)
       {
          this.trigger("onMovieUploaded", result);
       },
       onCamcorderStop: function(videoid)
       {
           this.$el.find(".camcorder-webcam-recording, .camcorder-movie-rec-stop").addClass("hidden");
           this.$el.find(".camcorder-movie-play, .camcorder-movie-rec-start").removeClass("hidden");

           window.setTimeout((function() {
               if(videoid == this.videoid)
               {
                   $.getJSON("/api/v1/movie/uploadRed5/" + videoid + "?noci=1", this.onMovieUploaded.bind(this));
               }
           }).bind(this), 500);
       },
       onCamcorderStart: function(videoid)
       {
           console.log("start recording");

           //.camcorder-movie-play,  addClass("hidden")

           this.$el.find(".camcorder-webcam-recording, .camcorder-movie-rec-stop").removeClass("hidden");
           this.$el.find(".camcorder-movie-stop, .camcorder-movie-rec-start, .camcorder-movie-play").addClass("hidden");
       },
       onCamcorderMovieStoped: function(videoid)
       {
           this.$el.find(".camcorder-movie-stop").addClass("hidden");
           this.$el.find(".camcorder-movie-play").removeClass("hidden");
       },
       show: function()
       {
           this.$el.find(".camcorder-show").addClass("hidden");
           this.$el.find(".camcorder-container-recorder").removeClass("hidden");
       },
       startRecord: function()
       {
           this.$el.find("object")[0].startRecord();
       },
       stopRecord: function()
       {
           this.$el.find("object")[0].stopRecord();
       },
       playMovie: function()
       {
           this.stopRecord();

           this.$el.find("object")[0].playMovie();
           this.$el.find(".camcorder-movie-play").addClass("hidden");
           this.$el.find(".camcorder-movie-stop").removeClass("hidden");
       },
       stopMovie: function()
       {
           this.$el.find("object")[0].stopMovie();
       },
       webcamParameters: function()
       {
           this.$el.find("object")[0].webcamParameters();
       },
       generateFlashObject: function(mode)
        {
            if(typeof mode == "undefined") mode = "record";

            var flashobject = "";

            flashobject += '<div style="width: 323px; margin: auto; position: relative;" class="hidden camcorder-container-recorder">';
                flashobject += '<object type="application/x-shockwave-flash" data="/flash/red5recorder.swf" width="323" height="200">';
                flashobject += '<param name="movie" value="/flash/red5recorder.swf" />';
                flashobject += '<param name="flashVars" value="height=200&server=rtmp://appartoo.com/red5recorder/&fileName='+ this.videoid +'&showVolume=false&recordingText=Enregistrement...&timeLeftText=Temprestant&maxLength=60&mode=' + mode + '" />';
                flashobject += '<param name="allowFullScreen" value="true" />';
                flashobject += '</object>';
                flashobject += '<div style="position: absolute; right: 5px; top: 5px; font-weight: bold;" class="text-danger camcorder-webcam-recording hidden">Enregistrement...</div>';
                flashobject += '<div style="position: absolute; left: 5px; top: 5px; font-weight: bold; font-size: 15px;" class="text-danger camcorder-webcam-parameter"><i class="glyphicon glyphicon-facetime-video"></i></div>';
                    flashobject += '<div style="background: #D8D9D9">';
                    flashobject += '<button type="button" class="btn btn-danger camcorder-movie-rec-start" style="margin: 10px;"><i class="glyphicon glyphicon-record"></i><span class="padding-5">Enregistrer</span></button>';
                    flashobject += '<button type="button"  class="btn btn-success camcorder-movie-rec-stop hidden" style="margin: 10px;"><i class="glyphicon glyphicon-stop"></i><span class="padding-5">Arreter l\'enregistrement</span></button>';
                    flashobject += '<button type="button"  class="btn btn-success camcorder-movie-play hidden" style="margin: 10px;"><i class="glyphicon glyphicon-play"></i><span class="padding-5">Lire</span></button>';
                    flashobject += '<button type="button"  class="btn btn-success camcorder-movie-stop hidden" style="margin: 10px;"><i class="glyphicon glyphicon-stop"></i><span class="padding-5">Stop</span></button>';
                    flashobject += '<div class="timeleft" style="display: inline-block; color: white; padding-left: 5px; font-size: 18px;"></div>';
                flashobject += '</div>';
            flashobject += '</div>';
            flashobject += '<div class="camcorder-show text-center"><button type="button" class="btn btn-info">' + app.getContainer().get("translator").trans("action.recordmovieflash") + '</button></div>';

            return flashobject;
        },
       render: function()
       {
           if(App.Tools.detectFlash())
           {
               var $recorderflash = this.$el.find("#recorder-flash");

               var flashobject = this.generateFlashObject();

               $recorderflash.html(flashobject);
           }
           else
           {
               this.initializeUploader();
           }
       }
   });

