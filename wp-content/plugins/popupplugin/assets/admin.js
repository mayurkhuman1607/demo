jQuery(document).ready(function($) {
  // Add media library trigger to add video button
  $('#pvg-video-add').click(function(e) {
    e.preventDefault();
    var videoFrame;
    if ( videoFrame ) {
      videoFrame.open();
      return;
    }
    videoFrame = wp.media.frames.videoFrame = wp.media({
      title: 'Select or upload videos',
      button: {
        text: 'Use this video'
      },
      multiple: true
    });
    videoFrame.on('select', function() {
      var selection = videoFrame.state().get('selection').toJSON();
      var videoIds = $('#pvg_video_ids').val();
      $.each( selection, function( key, value ) {
        videoIds += value.id + ',';
        $('#pvg-video-wrapper').append('<div class="pvg-video" data-video-id="' + value.id + '"><span class="pvg-video-title">' + value.
