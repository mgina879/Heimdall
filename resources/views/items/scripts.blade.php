<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
        $( function() {

            var base = (document.querySelector('base') || {}).href;

            var elem = $('.color-picker')[0];
            var hueb = new Huebee( elem, {
              // options
              setBGColor: '.set-bg-elem'
            });

            hueb.on( 'change', function( color, hue, sat, lum ) {
                $.get('{{ route('titlecolour') }}', {color}, function(data) {
                    $('#tile-preview .title').removeClass("black white");
                    $('#tile-preview .link').removeClass("black white");
                    $('#tile-preview .title').addClass(data);
                    $('#tile-preview .link').addClass(data);
                });
            })

            var availableTags = @json(App\Application::autocomplete());
            console.log(availableTags)
            $( "#appname" ).autocomplete({
                source: availableTags,
                select: function( event, ui ) {
                    event.preventDefault();
                    // appload(ui.item.value);
                    $( "#appname" ).val(ui.item.label)
                    $('#apptype').val(ui.item.value).change()
                }
            });
            // initial load
            $('#tile-preview .title').html($('#appname').val());
            $('#tile-preview .item').css('backgroundColor', $('#appcolour').val());
            $('#tile-preview .app-icon').attr('src', $('#appimage img').attr('src'));

            // Updates
            $('#appname').on('keyup change', function(e) {
                $('#tile-preview .title').html($(this).val());
            })
            $('#apptype').on('change', function(e) {
                appload($(this).find('option:selected').val());
            });
            $('#appcolour').on('change', function(e) {
                $('#tile-preview .item').css('backgroundColor', $(this).val());
            })

            $('.tags').select2();

            function appload(appvalue) {
                if(appvalue == 'null') {
                    $('#sapconfig').html('').hide();
                    $('#tile-preview .app-icon').attr('src', '/img/heimdall-icon-small.png');
                    $('#appimage').html("<img src='/img/heimdall-icon-small.png' />");
                    $('#sapconfig').html('').hide();
                } else {
                    $.post('{{ route('appload') }}', { app: appvalue }, function(data) {
                        // Main details
                        $('#appimage').html("<img src='"+data.iconview+"' /><input type='hidden' name='icon' value='"+data.iconview+"' />");
                        $('input[name=colour]').val(data.colour);
                        $('select[name=appid]').val(data.appid);
                        hueb.setColor( data.colour );
                        $('input[name=pinned]').prop('checked', true);
                        // Preview details
                        $('#tile-preview .app-icon').attr('src', data.iconview);
                        $('#tile-preview .title').html(data.name);
                        if(data.custom != null) {
                            $.get(base+'view/'+data.custom, function(getdata) {
                                $('#sapconfig').html(getdata).show();
                            });
                        } else {
                            $('#sapconfig').html('').hide();
                        }
                    }, "json");
                }

            }

        });
</script>
